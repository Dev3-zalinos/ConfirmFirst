<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Botble\RealEstate\Enums\CouponTypeEnum;

class Coupon extends BaseModel
{
    protected $table = 're_coupons';

    protected $fillable = [
        'type',
        'code',
        'value',
        'quantity',
        'total_used',
        'expires_date',
    ];

    protected $casts = [
        'type' => CouponTypeEnum::class,
        'value' => 'decimal:2',
        'quantity' => 'int',
        'total_used' => 'int',
        'expires_date' => 'datetime',
    ];
}
