<?php

namespace Botble\RealEstate\Forms\Settings;

use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\Fields\GoogleFontsField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\RealEstate\Http\Requests\Settings\InvoiceSettingRequest;
use Botble\RealEstate\Supports\InvoiceHelper;
use Botble\Setting\Forms\SettingForm;

class InvoiceSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/real-estate::settings.invoice.name'))
            ->setSectionDescription(trans('plugins/real-estate::settings.invoice.description'))
            ->addCustomField('googleFonts', GoogleFontsField::class)
            ->setValidatorClass(InvoiceSettingRequest::class)
            ->add('real_estate_company_name_for_invoicing', 'text', [
                'label' => trans('plugins/real-estate::settings.invoice.form.company_name'),
                'value' => setting('real_estate_company_name_for_invoicing', theme_option('site_title')),
            ])
            ->add('real_estate_company_address_for_invoicing', 'text', [
                'label' => trans('plugins/real-estate::settings.invoice.form.company_address'),
                'value' => setting('real_estate_company_address_for_invoicing', theme_option('site_title')),
            ])
            ->add('real_estate_company_email_for_invoicing', 'text', [
                'label' => trans('plugins/real-estate::settings.invoice.form.company_email'),
                'value' => setting('real_estate_company_email_for_invoicing', get_admin_email()->first()),
            ])
            ->add('real_estate_company_phone_for_invoicing', 'text', [
                'label' => trans('plugins/real-estate::settings.invoice.form.company_phone'),
                'value' => setting('real_estate_company_phone_for_invoicing'),
            ])
            ->add('real_estate_company_logo_for_invoicing', 'mediaImage', [
                'label' => trans('plugins/real-estate::settings.invoice.form.company_logo'),
                'value' => setting('real_estate_company_logo_for_invoicing') ?: theme_option('logo'),
            ])
            ->add('real_estate_using_custom_font_for_invoice', 'onOffCheckbox', [
                'label' => trans('plugins/real-estate::settings.invoice.form.using_custom_font_for_invoice'),
                'value' => setting('real_estate_using_custom_font_for_invoice', false),
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '.custom-font-for-invoice-settings',
                ],
            ])
            ->add('open_fieldset_custom_font_for_invoice_settings', 'html', [
                'html' => sprintf(
                    '<fieldset class="custom-font-for-invoice-settings form-fieldset"
                    data-bb-value="1"
                    style="display: %s"/>',
                    old(
                        'real_estate_using_custom_font_for_invoice',
                        setting('real_estate_using_custom_font_for_invoice', false)
                    ) ? 'block' : 'none',
                ),
            ])
            ->add('real_estate_invoice_font_family', 'googleFonts', [
                'label' => trans('plugins/real-estate::settings.invoice.form.invoice_font_family'),
                'selected' => setting('real_estate_invoice_font_family'),
            ])
            ->add('close_fieldset_custom_font_for_invoice_settings', 'html', [
                'html' => '</fieldset>',
            ])
            ->add('real_estate_invoice_support_arabic_language', 'onOffCheckbox', [
                'label' => trans('plugins/real-estate::settings.invoice.form.invoice_support_arabic_language'),
                'value' => setting('real_estate_invoice_support_arabic_language', false),
            ])
            ->add('real_estate_enable_invoice_stamp', 'onOffCheckbox', [
                'label' => trans('plugins/real-estate::settings.invoice.form.enable_invoice_stamp'),
                'value' => setting('real_estate_enable_invoice_stamp', true),
            ])
            ->add('real_estate_invoice_code_prefix', 'text', [
                'label' => trans('plugins/real-estate::settings.invoice.form.invoice_code_prefix'),
                'value' => setting('real_estate_invoice_code_prefix', 'INV-'),
            ])
            ->add(
                'invoice_language_support',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.invoice.form.add_language_support'))
                    ->choices([
                        'default' => trans('plugins/real-estate::settings.invoice.form.only_latin_languages'),
                        'arabic' => 'Arabic',
                        'bangladesh' => 'Bangladesh',
                        'chinese' => 'Chinese',
                    ])
                    ->defaultValue('default')
                    ->when(
                        (new InvoiceHelper())->getLanguageSupport(),
                        function (RadioFieldOption $option, string $language): void {
                            $option->selected($language);
                        }
                    )
                    ->colspan(6)
                    ->toArray()
            );
    }
}
