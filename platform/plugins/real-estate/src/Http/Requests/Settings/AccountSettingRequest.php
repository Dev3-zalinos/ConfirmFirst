<?php

namespace Botble\RealEstate\Http\Requests\Settings;

use Botble\Base\Rules\MediaImageRule;
use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class AccountSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'real_estate_enabled_login' => $onOffRule = new OnOffRule(),
            'real_estate_enabled_register' => $onOffRule,
            'verify_account_email' => $onOffRule,
            'real_estate_make_account_phone_number_required' => $onOffRule,
            'real_estate_enable_credits_system' => $onOffRule,
            'enable_post_approval' => $onOffRule,
            'allow_customizing_post_url' => $onOffRule,
            'real_estate_max_filesize_upload_by_agent' => $intRule = ['required', 'int', 'min:1'],
            'real_estate_max_property_images_upload_by_agent' => $intRule,
            'property_expired_after_days' => $intRule,
            'real_estate_enable_wishlist' => $onOffRule,
            'real_estate_hide_agency_phone' => $onOffRule,
            'real_estate_hide_agency_email' => $onOffRule,
            'real_estate_hide_agent_info_in_property_detail_page' => $onOffRule,
            'real_estate_disabled_public_profile' => $onOffRule,
            'real_estate_enable_account_verification' => $onOffRule,
            'real_estate_hide_username_in_registration_page' => $onOffRule,
            'real_estate_account_default_avatar' => ['nullable', new MediaImageRule()],
        ];
    }
}
