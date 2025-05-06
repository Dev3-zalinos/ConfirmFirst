<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\OnOffRule;
use Botble\Captcha\Facades\Captcha;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\ConsultCustomField;
use Botble\Support\Http\Requests\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SendConsultRequest extends Request
{
    protected Collection $customFields;

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:220'],
            'email' => ['nullable', 'email'],
            'phone' => 'nullable|string|' . BaseHelper::getPhoneValidationRule(),
            'content' => ['required', 'string'],
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();
        }

        $availableMandatoryFields = RealEstateHelper::enabledMandatoryFieldsAtConsultForm();
        $hiddenFields = RealEstateHelper::getHiddenFieldsAtConsultForm();

        if ($hiddenFields) {
            Arr::forget($rules, $hiddenFields);
        }

        if ($availableMandatoryFields) {
            foreach ($availableMandatoryFields as $value) {
                if (! isset($rules[$value])) {
                    continue;
                }

                if (is_string($rules[$value])) {
                    $rules[$value] = str_replace('nullable', 'required', $rules[$value]);

                    continue;
                }

                if (is_array($rules[$value])) {
                    $rules[$value] = array_merge(['required'], array_filter($rules[$value], fn ($item) => $item !== 'nullable'));
                }
            }
        }

        $customFields = $this->getCustomFields();

        if ($customFields->isNotEmpty()) {
            $rules['consult_custom_fields'] = ['required', 'array'];
        }

        foreach ($customFields as $customField) {
            $customFieldRules = [$customField->required ? 'required' : 'nullable'];

            $rules["consult_custom_fields.$customField->id"] = match ($customField->type->getValue()) {
                ConsultCustomFieldTypeEnum::TEXT, ConsultCustomFieldTypeEnum::DROPDOWN, ConsultCustomFieldTypeEnum::RADIO, ConsultCustomFieldTypeEnum::DATE => [...$customFieldRules, 'string', 'max:255'],
                ConsultCustomFieldTypeEnum::TEXTAREA => [...$customFieldRules, 'string', 'max:1000'],
                ConsultCustomFieldTypeEnum::NUMBER => [...$customFieldRules, 'numeric'],
                ConsultCustomFieldTypeEnum::CHECKBOX => [new OnOffRule()],
                default => $customFieldRules,
            };
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'name' => __('Name'),
            'phone' => __('Phone'),
            'email' => __('Email'),
            'content' => __('Content'),
        ] + (is_plugin_active('captcha') ? Captcha::attributes() : []);

        $customFields = $this->getCustomFields();

        foreach ($customFields as $customField) {
            $attributes["consult_custom_fields.$customField->id"] = $customField->name;
        }

        return $attributes;
    }

    protected function getCustomFields(): Collection
    {
        if (isset($this->customFields)) {
            return $this->customFields;
        }

        return $this->customFields = ConsultCustomField::query()
            ->wherePublished()
            ->with('options')
            ->get();
    }
}
