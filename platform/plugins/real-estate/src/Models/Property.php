<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Traits\UniqueId;
use Botble\RealEstate\QueryBuilders\PropertyBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;

/**
 * @method static \Botble\RealEstate\QueryBuilders\PropertyBuilder<static> query()
 */
class Property extends BaseModel
{
    use UniqueId;

    protected $table = 're_properties';

    protected $fillable = [
        'name',
        'type',
        'description',
        'content',
        'location',
        'images',
        'project_id',
        'number_bedroom',
        'number_bathroom',
        'number_floor',
        'square',
        'price',
        'status',
        'is_featured',
        'featured_priority',
        'currency_id',
        'city_id',
        'state_id',
        'country_id',
        'period',
        'author_id',
        'author_type',
        'expire_date',
        'auto_renew',
        'latitude',
        'longitude',
        'unique_id',
        'private_notes',
        'floor_plans',
        'reject_reason',
    ];

    protected $casts = [
        'status' => PropertyStatusEnum::class,
        'moderation_status' => ModerationStatusEnum::class,
        'type' => PropertyTypeEnum::class,
        'period' => PropertyPeriodEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'content' => SafeContent::class,
        'location' => SafeContent::class,
        'private_notes' => SafeContent::class,
        'expire_date' => 'datetime',
        'images' => 'json',
        'price' => 'float',
        'square' => 'float',
        'number_bedroom' => 'int',
        'number_bathroom' => 'int',
        'number_floor' => 'int',
        'featured_priority' => 'int',
        'floor_plans' => 'array',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Property $property): void {
            $property->categories()->detach();
            $property->customFields()->delete();
            $property->reviews()->delete();
            $property->features()->detach();
            $property->facilities()->detach();
            $property->metadata()->delete();
        });

        // Add real-time expiration check
        static::retrieved(function (Property $property): void {
            // Skip if not approved or not an account property
            if ($property->moderation_status !== ModerationStatusEnum::APPROVED || 
                $property->author_type !== Account::class) {
                return;
            }

            // Get the latest approval activity log
            $latestApproval = AccountActivityLog::query()
                ->where('account_id', $property->author_id)
                ->where('action', 'property_approved')
                ->where('reference_url', 'LIKE', '%' . $property->id . '%')
                ->latest()
                ->first();

            if (!$latestApproval) {
                return;
            }

            // Calculate minutes since last approval
            $minutesPassed = Carbon::now()->diffInMinutes($latestApproval->created_at);

            // If 3 minutes have passed since last approval
            if ($minutesPassed >= 3) {
                $lockKey = 'property_credit_lock_' . $property->id;
                
                if (!cache()->has($lockKey)) {
                    try {
                        cache()->put($lockKey, true, 1); // Shorter lock time
                        
                        // Calculate credits needed regardless of never_expired status
                        $creditsNeeded = $property->calculateCreditsNeeded();
                        
                        if ($property->never_expired) {
                            // For never_expired properties, attempt to deduct credits and renew
                            if ($property->author && $property->author->credits >= $creditsNeeded) {
                                // Deduct credits
                                $property->author->credits -= $creditsNeeded;
                                $property->author->save();
                                
                                // Update property expiration and create new cycle
                                $property->expire_date = Carbon::now()->addMinutes(3);
                                $property->save();
                                
                                // Create credit deduction log
                                AccountActivityLog::query()->create([
                                    'action' => 'property_renewed',
                                    'reference_name' => $property->name . ' (-' . $creditsNeeded . ' credits)',
                                    'reference_url' => route('public.account.properties.edit', $property->id),
                                    'account_id' => $property->author_id
                                ]);
                                
                                // Create new approval log for next cycle
                                AccountActivityLog::query()->create([
                                    'action' => 'property_approved',
                                    'reference_name' => $property->name,
                                    'reference_url' => route('public.account.properties.edit', $property->id),
                                    'account_id' => $property->author_id
                                ]);
                            } else {
                                // Not enough credits, mark as expired
                                $property->moderation_status = ModerationStatusEnum::EXPIRED;
                                $property->save();
                                
                                // Log the expiration
                                AccountActivityLog::query()->create([
                                    'action' => 'property_expired',
                                    'reference_name' => $property->name . ' (insufficient credits)',
                                    'reference_url' => route('public.account.properties.edit', $property->id),
                                    'account_id' => $property->author_id
                                ]);
                            }
                        } else {
                            // For non-never_expired properties, mark as expired after 3 minutes
                            $property->moderation_status = ModerationStatusEnum::EXPIRED;
                            $property->save();
                            
                            // Log the expiration
                            AccountActivityLog::query()->create([
                                'action' => 'property_expired',
                                'reference_name' => $property->name,
                                'reference_url' => route('public.account.properties.edit', $property->id),
                                'account_id' => $property->author_id
                            ]);
                        }
                    } finally {
                        cache()->forget($lockKey);
                    }
                }
            }
        });
    }

    /**
     * Calculate credits needed based on property price
     */
    public function calculateCreditsNeeded(): int
    {
        return max(1, (int)ceil($this->price / 1000000));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id')->withDefault();
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 're_property_features', 'property_id', 'feature_id');
    }

    public function facilities(): BelongsToMany
    {
        return $this->morphToMany(Facility::class, 'reference', 're_facilities_distances')->withPivot('distance');
    }

    protected function image(): Attribute
    {
        return Attribute::get(fn () => Arr::first($this->images) ?? null);
    }

    protected function squareText(): Attribute
    {
        return Attribute::get(function () {
            $square = $this->square;

            $unit = setting('real_estate_square_unit', 'm²');

            return apply_filters('real_estate_property_square_text', sprintf('%s %s', number_format($square), __($unit)), $square);
        });
    }

    protected function address(): Attribute
    {
        return Attribute::get(fn () => $this->location);
    }

    protected function category(): Attribute
    {
        return Attribute::get(fn () => $this->categories->first() ?: new Category());
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 're_property_categories');
    }

    protected function cityName(): Attribute
    {
        return Attribute::get(function () {
            if (! is_plugin_active('location')) {
                return $this->location;
            }

            return ($this->city->name ? $this->city->name . ', ' : null) . $this->state->name;
        });
    }

    protected function typeHtml(): Attribute
    {
        return Attribute::get(fn () => $this->type->label());
    }

    protected function statusHtml(): Attribute
    {
        return Attribute::get(fn () => $this->status->toHtml());
    }

    protected function categoryName(): Attribute
    {
        return Attribute::get(fn () => $this->category->name);
    }

    protected function imageThumb(): Attribute
    {
        return Attribute::get(fn () => $this->image ? RvMedia::getImageUrl($this->image, 'thumb', false, RvMedia::getDefaultImage()) : null);
    }

    protected function imageSmall(): Attribute
    {
        return Attribute::get(fn () => $this->image ? RvMedia::getImageUrl($this->image, 'small', false, RvMedia::getDefaultImage()) : null);
    }

    protected function priceHtml(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->price) {
                return __('Contact');
            }

            $price = $this->price_format;

            if ($this->type == PropertyTypeEnum::RENT) {
                $price .= ' / ' . Str::lower($this->period->shortLabel());
            }

            return $price;
        });
    }

    protected function priceFormat(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->price) {
                return __('Contact');
            }

            if ($this->price_formatted) {
                return $this->price_formatted;
            }

            $currency = $this->currency;

            if (! $currency || ! $currency->getKey()) {
                $currency = get_application_currency();
            }

            return $this->price_formatted = format_price($this->price, $currency);
        });
    }

    protected function mapIcon(): Attribute
    {
        return Attribute::get(fn () => $this->type_html . ': ' . $this->price_format);
    }

    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'reference', 'reference_type', 'reference_id')->with('customField.options');
    }

    protected function customFieldsArray(): Attribute
    {
        return Attribute::get(fn () => CustomFieldValue::getCustomFieldValuesArray($this));
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function newEloquentBuilder($query): PropertyBuilder
    {
        return new PropertyBuilder($query);
    }

    protected function shortAddress(): Attribute
    {
        return Attribute::get(function () {
            if (! is_plugin_active('location')) {
                return $this->location;
            }

            return implode(', ', array_filter([$this->city->name, $this->state->name]));
        });
    }

    protected function formattedFloorPlans(): Attribute
    {
        return Attribute::get(function () {
            $floorPlan = $this->floor_plans;

            if (! is_array($floorPlan)) {
                $floorPlan = json_decode($floorPlan, true);
            }

            return collect($floorPlan)
                ->filter(fn ($floorPlan) => is_array($floorPlan))
                ->map(function ($floorPlan) {
                    $floorPlan = collect($floorPlan)->pluck('value', 'key')->toArray();
                    $bedrooms = (int) Arr::get($floorPlan, 'bedrooms', 0);
                    $bathrooms = (int) Arr::get($floorPlan, 'bathrooms', 0);

                    return [
                        'name' => Arr::get($floorPlan, 'name'),
                        'description' => Arr::get($floorPlan, 'description'),
                        'image' => Arr::get($floorPlan, 'image'),
                        'bedrooms' => $bedrooms === 1 ? __('1 bedroom') : __(':count bedrooms', ['count' => $bedrooms]),
                        'bathrooms' => $bathrooms === 1 ? __('1 bathroom') : __(':count bathrooms', ['count' => $bathrooms]),
                    ];
                });
        });
    }

    protected function isPendingModeration(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->exists) {
                return false;
            }

            return ! in_array($this->moderation_status, [ModerationStatusEnum::APPROVED, ModerationStatusEnum::REJECTED]);
        });
    }

    /**
     * Check if property has expired
     * 
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->never_expired) {
            return false;
        }

        if (empty($this->expire_date)) {
            return true;
        }

        return Carbon::parse($this->expire_date)->isPast();
    }

    /**
     * Check if property is visible to the public
     * 
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->status != PropertyStatusEnum::DRAFT
            && $this->moderation_status == ModerationStatusEnum::APPROVED
            && !$this->isExpired();
    }
}