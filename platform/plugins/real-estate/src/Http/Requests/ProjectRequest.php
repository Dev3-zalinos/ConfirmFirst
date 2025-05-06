<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ProjectRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:400'],
            'content' => ['nullable', 'string', 'max:100000'],
            'number_block' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'number_floor' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'number_flat' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'price_from' => ['numeric', 'min:0', 'nullable', 'max:' . 1_000_000_000_000],
            'price_to' => ['numeric', 'min:0', 'nullable', 'max:' . 1_000_000_000_000],
            'latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'status' => Rule::in(ProjectStatusEnum::values()),
            'unique_id' => 'nullable|string|max:120|unique:re_projects,unique_id,' . $this->route('project'),
            'date_finish' => ['nullable', 'date'],
            'date_sell' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:191'],
            'facilities' => ['nullable', 'array'],
            'facilities.*.id' => ['required', 'numeric', 'exists:re_facilities,id'],
            'facilities.*.distance' => ['required', 'string', 'max:50'],
            'private_notes' => ['nullable', 'string', 'max:10000'],
            'custom_fields.*.name' => ['required', 'string', 'max:255'],
            'custom_fields.*.value' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'facilities.*.distance' => trans('plugins/real-estate::property.distance_key'),
            'custom_fields.*.name' => trans('plugins/real-estate::custom-fields.name'),
            'custom_fields.*.value' => trans('plugins/real-estate::custom-fields.name'),
        ];
    }
}
