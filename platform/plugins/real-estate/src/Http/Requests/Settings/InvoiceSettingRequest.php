<?php

namespace Botble\RealEstate\Http\Requests\Settings;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\GoogleFontsRule;
use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class InvoiceSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'real_estate_company_name_for_invoicing' => ['nullable', 'string', 'max:120'],
            'real_estate_company_address_for_invoicing' => ['nullable', 'string', 'max:255'],
            'real_estate_company_email_for_invoicing' => ['nullable', 'email'],
            'real_estate_company_phone_for_invoicing' => 'sometimes|' . BaseHelper::getPhoneValidationRule(),
            'real_estate_company_logo_for_invoicing' => ['nullable', 'string', 'max:255'],
            'real_estate_using_custom_font_for_invoice' => $onOffRule = new OnOffRule(),
            'real_estate_invoice_font_family' => ['nullable', new GoogleFontsRule(), 'required_if:real_estate_using_custom_font_for_invoice, 1'],
            'real_estate_invoice_support_arabic_language' => $onOffRule,
            'real_estate_enable_invoice_stamp' => $onOffRule,
            'real_estate_invoice_code_prefix' => ['nullable', 'string', 'max:120'],
        ];
    }
}
