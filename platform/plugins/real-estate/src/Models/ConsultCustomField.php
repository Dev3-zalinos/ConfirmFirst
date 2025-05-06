<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultCustomField extends BaseModel
{
    protected $table = 're_consult_custom_fields';

    protected $fillable = [
        'name',
        'required',
        'placeholder',
        'type',
        'status',
        'order',

    ];

    protected $casts = [
        'type' => ConsultCustomFieldTypeEnum::class,
        'required' => 'bool',
        'order' => 'int',
        'status' => BaseStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::deleting(fn (ConsultCustomField $customField) => $customField->options()->delete());
    }

    public function options(): HasMany
    {
        return $this->hasMany(ConsultCustomFieldOption::class, 'custom_field_id');
    }

    public function saveOptions(array $options): void
    {
        $formattedOptions = [];

        $this
            ->options()
            ->whereNotIn('id', array_column($options, 'id'))
            ->delete();

        foreach ($options as $item) {
            $option = null;

            if (isset($item['id'])) {
                $option = $this->options()->find($item['id']);
                $option->fill($item);
            }

            if (! $option) {
                $option = new ConsultCustomFieldOption($item);
            }

            if ($option->isDirty()) {
                $formattedOptions[] = $option;
            }
        }

        if (! empty($formattedOptions)) {
            $this->options()->saveMany($formattedOptions);
        }
    }
}
