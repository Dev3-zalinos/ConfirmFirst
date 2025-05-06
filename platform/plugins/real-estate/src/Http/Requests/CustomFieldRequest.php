<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Models\CustomField;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends Request
{
    public function rules(): array
    {
        $isDropdownField = $this->input('type') === CustomFieldEnum::DROPDOWN;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(CustomField::class)->ignore($this->custom_field, 'id'),
            ],
            'type' => ['required', 'string'],
            'is_global' => ['required', 'boolean'],
            'options.*.id' => [
                'sometimes',
            ],
            'options.*.label' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => $isDropdownField),
            ],
            'options.*.value' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => $isDropdownField),
            ],
            'options.*.order' => [
                'numeric',
                'min:0',
                'max:999',
            ],
        ];
    }
}
