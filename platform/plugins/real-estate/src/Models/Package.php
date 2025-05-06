<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends BaseModel
{
    protected $table = 're_packages';

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'percent_save',
        'number_of_listings',
        'account_limit',
        'order',
        'features',
        'is_default',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'features' => 'json',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 're_account_packages', 'package_id', 'account_id');
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->price - ($this->price * $this->percent_save / 100);
    }

    public function getPriceTextAttribute(): string
    {
        return format_price($this->price, $this->currency);
    }

    public function getPricePerPostTextAttribute(): string
    {
        return __(':price / per post', ['price' => format_price($this->price / $this->number_of_listings, $this->currency)]);
    }

    public function getNumberPostsFreeAttribute(): string
    {
        return __('Free :number post(s)', ['number' => $this->number_of_listings]);
    }

    public function getPriceTextWithSaleOffAttribute(): string
    {
        return __(':price Total :percentage_sale', ['price' => $this->price_text, 'percentage_sale' => $this->percent_save_text]);
    }

    public function getPercentSaveTextAttribute(): string
    {
        $text = '';

        if ($this->percent_save) {
            $text .= ' ' . __('save :percentage %', ['percentage' => $this->percent_save]);
        }

        return $text;
    }

    public function isPurchased(): bool
    {
        return $this->account_limit && $this->accounts_count >= $this->account_limit;
    }

    protected function formattedFeatures(): Attribute
    {
        return Attribute::get(
            fn () => collect(is_array($this->features) ? $this->features : json_decode($this->features, true))
                ->map(fn ($feature) => collect($feature)->pluck('value', 'key'))
                ->pluck('text')
                ->toArray()
        );
    }
}
