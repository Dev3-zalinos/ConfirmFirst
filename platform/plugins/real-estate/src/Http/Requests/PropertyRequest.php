<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PropertyRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:220'],
            'description' => ['nullable', 'string', 'max:400'],
            'content' => ['nullable', 'string', 'max:300000'],
            'number_bedroom' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'number_bathroom' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'number_floor' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'price' => ['numeric', 'min:0', 'nullable'],
            'latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'status' => Rule::in(PropertyStatusEnum::values()),
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),
            'custom_fields.*.name' => ['required', 'string', 'max:255'],
            'custom_fields.*.value' => ['nullable', 'string', 'max:255'],
            'unique_id' => 'nullable|string|max:120|unique:re_properties,unique_id,' . $this->route('property'),
            'location' => ['nullable', 'string', 'max:191'],
            'facilities' => ['nullable', 'array'],
            'facilities.*.id' => ['required', 'numeric', 'exists:re_facilities,id'],
            'facilities.*.distance' => ['required', 'string', 'max:50'],
            'private_notes' => ['nullable', 'string', 'max:10000'],
            'floor_plans' => ['nullable'],
        ];
    }

    public function attributes(): array
    {
        return [
            'facilities.*.distance' => trans('plugins/real-estate::property.distance_key'),
            'custom_fields.*.name' => trans('plugins/real-estate::custom-fields.name'),
            'custom_fields.*.value' => trans('plugins/real-estate::custom-fields.name'),
            'floor_plans' => trans('plugins/real-estate::property.floor_plans.title'),
        ];
    }
}
