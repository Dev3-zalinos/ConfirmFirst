<?php

namespace Botble\RealEstate\Http\Requests\Fronts\Auth;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\RealEstate\Models\Account;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120', 'min:2'],
            'username' => [
                Rule::requiredIf(fn () => ! setting('real_estate_hide_username_in_registration_page', false)),
                'string',
                'max:120',
                'min:2',
                Rule::unique((new Account())->getTable(), 'username'),
            ],
            'email' => ['required', 'max:60', 'min:6', new EmailRule(), 'unique:re_accounts'],
            'phone' => [
                'nullable',
                Rule::requiredIf((bool) setting('real_estate_make_account_phone_number_required', false)),
                ...explode('|', BaseHelper::getPhoneValidationRule()),
                Rule::unique((new Account())->getTable(), 'phone'),
            ],
            'password' => ['required', 'min:6', 'confirmed'],
            'agree_terms_and_policy' => ['sometimes', 'accepted:1'],
        ];
    }
}
