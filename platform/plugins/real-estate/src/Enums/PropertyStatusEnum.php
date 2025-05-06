<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static PropertyStatusEnum DRAFT()
 * @method static PropertyStatusEnum NOT_AVAILABLE()
 * @method static PropertyStatusEnum PRE_SALE()
 * @method static PropertyStatusEnum SELLING()
 * @method static PropertyStatusEnum SOLD()
 * @method static PropertyStatusEnum RENTING()
 * @method static PropertyStatusEnum RENTED()
 * @method static PropertyStatusEnum BUILDING()
 */
class PropertyStatusEnum extends Enum
{
    public const DRAFT = 'draft';

    public const NOT_AVAILABLE = 'not_available';

    public const PRE_SALE = 'pre_sale';

    public const SELLING = 'selling';

    public const SOLD = 'sold';

    public const RENTING = 'renting';

    public const RENTED = 'rented';

    public const BUILDING = 'building';

    public static $langPath = 'plugins/real-estate::property.statuses';

    public function toHtml(): HtmlString|string|null
    {
        if (! is_in_admin()) {
            $html = match ($this->value) {
                self::DRAFT => Html::tag('span', self::DRAFT()->label(), ['class' => 'label-default status-label'])
                    ->toHtml(),
                self::NOT_AVAILABLE => Html::tag(
                    'span',
                    self::NOT_AVAILABLE()->label(),
                    ['class' => 'label-default status-label']
                )
                    ->toHtml(),
                self::PRE_SALE => Html::tag('span', self::PRE_SALE()->label(), ['class' => 'label-success status-label'])
                    ->toHtml(),
                self::SELLING => Html::tag('span', self::SELLING()->label(), ['class' => 'label-success status-label'])
                    ->toHtml(),
                self::SOLD => Html::tag('span', self::SOLD()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml(),
                self::RENTING => Html::tag('span', self::RENTING()->label(), ['class' => 'label-success status-label'])
                    ->toHtml(),
                self::RENTED => Html::tag('span', self::RENTED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml(),
                self::BUILDING => Html::tag('span', self::BUILDING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml(),
                default => Html::tag('span', $this->label(), ['class' => 'label-default status-label'])->toHtml(),
            };

            return apply_filters('real_estate_property_status_html', $html, $this->value);
        }

        $color = match ($this->value) {
            self::NOT_AVAILABLE => 'secondary',
            self::PRE_SALE, self::SELLING, self::RENTING => 'success',
            self::SOLD, self::RENTED => 'danger',
            self::BUILDING => 'info',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
