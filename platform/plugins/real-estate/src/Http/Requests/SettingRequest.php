<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Language;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'username' => 'required|string|max:60|min:2|unique:re_accounts,username,' . auth('account')->id(),
            'company' => ['required', 'string', 'max:255'],
            'phone' => 'sometimes|' . BaseHelper::getPhoneValidationRule(),
            'dob' => ['max:20', 'sometimes'],
            'locale' => ['sometimes', 'required', Rule::in(array_keys(Language::getAvailableLocales()))],
        ];
    }
}