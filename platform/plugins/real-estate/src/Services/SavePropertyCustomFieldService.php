<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Models\Property;

class SavePropertyCustomFieldService
{
    public function execute(Property $property, array $customFields = []): void
    {
        $customFields = CustomFieldValue::formatCustomFields($customFields);

        $property
            ->customFields()
            ->whereNotIn('id', collect($customFields)->pluck('id')->all())
            ->delete();

        $property->customFields()->saveMany($customFields);
    }
}
