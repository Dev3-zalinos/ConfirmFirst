<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DuplicatePropertyController extends BaseController
{
    public function __invoke(Property $property)
    {
        $categories = $property->categories->pluck('id')->all();
        $facilities = $property->facilities()->get()->map(function ($facility) {
            return [
                'facility_id' => $facility->id,
                'distance' => $facility->pivot->distance,
            ];
        })->all();

        $features = $property->features->pluck('id')->all();

        $newProperty = $property->replicate();

        if ($newProperty->unique_id) {
            $newProperty->unique_id = $newProperty->unique_id . '-' . Str::random(5);
        }

        $newProperty->views = 0;
        $newProperty->created_at = Carbon::now();
        $newProperty->updated_at = Carbon::now();

        if (! $newProperty->never_expired) {
            $newProperty->expire_date = Carbon::now()->addMinutes(RealEstateHelper::propertyExpiredMinutes());
        }

        $newProperty->save();

        $newProperty->categories()->sync($categories);
        $newProperty->facilities()->sync($facilities);
        $newProperty->features()->sync($features);

        if (RealEstateHelper::isEnabledCustomFields() && $property->customFields()->exists()) {
            $customFields = $property->customFields()->get()->map(function ($customField) use ($newProperty) {
                return [
                    'custom_field_id' => $customField->id,
                    'name' => $customField->value,
                    'value' => $customField->value,
                    'reference_type' => Property::class,
                    'reference_id' => $newProperty->getKey(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            })->all();

            CustomFieldValue::query()->insert($customFields);
        }

        Slug::query()->create([
            'reference_type' => Property::class,
            'reference_id' => $newProperty->getKey(),
            'key' => Str::slug($newProperty->name) . '-' . $newProperty->getKey(),
            'prefix' => SlugHelper::getPrefix(Property::class),
        ]);

        return $this
            ->httpResponse()
            ->setData(['url' => route('property.edit', $newProperty->getKey())])
            ->setMessage(trans('plugins/real-estate::property.duplicate_property_successfully'));
    }
}
