<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static ConsultStatusEnum UNREAD()
 * @method static ConsultStatusEnum READ()
 */
class ConsultStatusEnum extends Enum
{
    public const READ = 'read';

    public const UNREAD = 'unread';

    public static $langPath = 'plugins/real-estate::consult.statuses';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::UNREAD => 'warning',
            self::READ => 'success',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
