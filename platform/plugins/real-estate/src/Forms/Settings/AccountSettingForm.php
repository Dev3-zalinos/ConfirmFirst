<?php

namespace Botble\RealEstate\Forms\Settings;

use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\Settings\AccountSettingRequest;
use Botble\Setting\Forms\SettingForm;

class AccountSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/real-estate::settings.account.name'))
            ->setSectionDescription(trans('plugins/real-estate::settings.account.description'))
            ->setValidatorClass(AccountSettingRequest::class)
            ->add(
                'real_estate_enabled_login',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.real_estate_enabled_login'))
                    ->value($enabledLogin = RealEstateHelper::isLoginEnabled())
            )
            ->addOpenCollapsible('real_estate_enabled_login', '1', $enabledLogin)
            ->add(
                'real_estate_enabled_register',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.real_estate_enabled_register'))
                    ->value(RealEstateHelper::isRegisterEnabled())
            )
            ->add(
                'verify_account_email',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.verify_account_email'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.verify_account_email_helper'))
                    ->value(setting('verify_account_email', false))
            )
            ->add(
                'real_estate_make_account_phone_number_required',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.make_account_phone_number_required'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.make_account_phone_number_required_helper'))
                    ->value((bool) setting('real_estate_make_account_phone_number_required', false))
            )
            ->add(
                'real_estate_hide_username_in_registration_page',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.hide_username_in_registration_page'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.hide_username_in_registration_page_helper'))
                    ->value((bool) setting('real_estate_hide_username_in_registration_page', false))
            )
            ->add(
                'real_estate_enable_credits_system',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.enable_credits_system'))
                    ->value(RealEstateHelper::isEnabledCreditsSystem())
            )
            ->add(
                'enable_post_approval',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.enable_post_approval'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.enable_post_approval_helper'))
                    ->value(setting('enable_post_approval', true))
            )
            ->add(
                'allow_customizing_post_url',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.allow_customizing_post_url'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.allow_customizing_post_url_helper'))
                    ->value(setting('allow_customizing_post_url', true))
            )
            ->add('real_estate_max_filesize_upload_by_agent', NumberField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.max_upload_filesize'),
                'value' => RealEstateHelper::maxFilesizeUploadByAgent(),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::settings.account.form.max_upload_filesize_placeholder', [
                        'size' => RealEstateHelper::maxFilesizeUploadByAgent(),
                    ]),
                ],
            ])
            ->add('real_estate_max_property_images_upload_by_agent', NumberField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.max_property_images_upload_by_agent'),
                'value' => RealEstateHelper::maxPropertyImagesUploadByAgent(),
            ])
            ->add(
                'real_estate_enable_account_verification',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.enable_account_verification'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.enable_account_verification_help'))
                    ->value((bool) setting('real_estate_enable_account_verification', false))
            )
            ->addCloseCollapsible('real_estate_enabled_login', '1')
            ->add('property_expired_after_days', NumberField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.property_expired_after_days'),
                'value' => RealEstateHelper::propertyExpiredDays(),
            ])
            ->add('real_estate_enable_wishlist', OnOffCheckboxField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.enable_wishlist'),
                'value' => setting('real_estate_enable_wishlist', true),
            ])
            ->add('real_estate_hide_agency_phone', OnOffCheckboxField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.hide_agency_phone'),
                'value' => setting('real_estate_hide_agency_phone', false),
            ])
            ->add('real_estate_hide_agency_email', OnOffCheckboxField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.hide_agency_email'),
                'value' => setting('real_estate_hide_agency_email', false),
            ])
            ->add('real_estate_hide_agent_info_in_property_detail_page', OnOffCheckboxField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.hide_agent_info_in_property_detail_page'),
                'value' => RealEstateHelper::hideAgentInfoInPropertyDetailPage(),
            ])
            ->add('real_estate_disabled_public_profile', OnOffCheckboxField::class, [
                'label' => trans('plugins/real-estate::settings.account.form.disabled_public_profile'),
                'value' => RealEstateHelper::isDisabledPublicProfile(),
            ])
            ->add(
                'real_estate_account_default_avatar',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.account.form.default_avatar'))
                    ->helperText(trans('plugins/real-estate::settings.account.form.default_avatar_helper'))
                    ->value(setting('real_estate_account_default_avatar'))
            );
    }
}
