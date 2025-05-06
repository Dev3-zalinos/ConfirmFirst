<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Botble\RealEstate\Models\ConsultCustomField;
use Botble\RealEstate\Models\ConsultCustomFieldOption;
use Illuminate\Support\Arr;

class ConsultSeeder extends BaseSeeder
{
    public function run(): void
    {
        ConsultCustomField::query()->truncate();
        ConsultCustomFieldOption::query()->truncate();

        $customFields = [
            [
                'name' => 'Schedule a Tour (optional)',
                'type' => ConsultCustomFieldTypeEnum::DATE,
                'required' => false,
            ],
        ];

        foreach ($customFields as $item) {
            $customField = ConsultCustomField::query()->create(Arr::except($item, 'options'));

            $customField->options()->createMany($item['options'] ?? []);
        }
    }
}
