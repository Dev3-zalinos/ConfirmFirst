<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static TransactionTypeEnum REMOVE()
 * @method static TransactionTypeEnum ADD()
 */
class TransactionTypeEnum extends Enum
{
    public const ADD = 'add';

    public const REMOVE = 'remove';

    public static $langPath = 'plugins/real-estate::transaction.types';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::REMOVE => 'warning',
            self::ADD => 'success',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
