<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class CustomFieldValue extends BaseModel
{
    protected $table = 're_custom_field_values';

    protected $fillable = [
        'name',
        'value',
        'reference_id',
        'reference_type',
        'custom_field_id',
    ];

    protected $casts = [
        'name' => SafeContent::class,
        'value' => SafeContent::class,
    ];

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public static function getCustomFieldValuesArray(BaseModel $object): array
    {
        $customFields = [];

        $addedCustomFields = $object->customFields;

        if (setting('real_estate_show_all_custom_fields_in_form_by_default', false)) {
            foreach (CustomField::query()->get() as $item) {
                if (! $addedCustomFields->contains('custom_field_id', $item->id)) {
                    $addedCustomFields->push(new CustomFieldValue([
                        'id' => $item->id,
                        'name' => $item->name,
                        'value' => $item->value,
                        'type' => $item->type ? $item->type->getValue() : $item->customField?->type->getValue(),
                        'custom_field_id' => $item->custom_field_id ?: $item->id,
                    ]));
                }
            }
        }

        foreach ($addedCustomFields as $item) {
            $customField = [];

            if ($item instanceof CustomField) {
                $item->customField = $item;
            }

            if ($item->customField) {
                $customField = [
                    'id' => $item->customField->id,
                    'name' => $item->customField->name,
                    'type' => $item->type ? $item->type->getValue() : $item->customField?->type->getValue(),
                    'options' => [],
                ];

                foreach ($item->customField->options as $option) {
                    $customField['options'][] = [
                        'id' => $option->id,
                        'label' => $option->label,
                        'value' => $option->value,
                    ];
                }
            }

            $customFields[] = [
                'id' => $item->id,
                'name' => $item->name,
                'value' => $item->value,
                'type' => $item->type ? $item->type->getValue() : $item->customField?->type->getValue(),
                'custom_field_id' => $item->custom_field_id ?: $item->id,
                'custom_field' => $customField,
            ];
        }

        return $customFields;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $this->customField) {
                    return $value;
                }

                return $this->customField->name;
            },
        );
    }

    public static function formatCustomFields(array $customFields = []): array
    {
        $newCustomFields = [];

        foreach ($customFields as $item) {
            $customField = null;

            if ($item['id']) {
                $customField = self::query()->findOrNew($item['id']);
                $customField->fill($item);
            } else {
                Arr::forget($item, 'id');
                $customField = new self($item);
            }

            $newCustomFields[] = $customField;
        }

        return $newCustomFields;
    }
}
