<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static PropertyTypeEnum SALE()
 * @method static PropertyTypeEnum RENT()
 */
class PropertyTypeEnum extends Enum
{
    public const SALE = 'sale';

    public const RENT = 'rent';

    public static $langPath = 'plugins/real-estate::property.types';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::SALE => 'success',
            self::RENT => 'info',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
