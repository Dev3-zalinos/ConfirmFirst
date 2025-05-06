<?php

namespace Botble\RealEstate\Forms\Settings;

use Botble\Base\Facades\Assets;
use Botble\RealEstate\Http\Requests\Settings\CurrencySettingRequest;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\Setting\Forms\SettingForm;

class CurrencySettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScripts('jquery-ui')
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/currencies.js')
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/currencies.css');

        $currencies = app(CurrencyInterface::class)
            ->getAllCurrencies()
            ->toArray();

        $this
            ->setSectionTitle(trans('plugins/real-estate::settings.currency.name'))
            ->setSectionDescription(trans('plugins/real-estate::settings.currency.description'))
            ->contentOnly()
            ->setFormOptions([
                'class' => 'main-setting-form',
            ])
            ->setValidatorClass(CurrencySettingRequest::class)
            ->add('real_estate_convert_money_to_text_enabled', 'onOffCheckbox', [
                'label' => trans('plugins/real-estate::settings.currency.form.real_estate_convert_money_to_text_enabled'),
                'value' => setting(
                    'real_estate_convert_money_to_text_enabled',
                    config('plugins.real-estate.real-estate.display_big_money_in_million_billion')
                ),
            ])
            ->add('real_estate_enable_auto_detect_visitor_currency', 'onOffCheckbox', [
                'label' => trans('plugins/real-estate::settings.currency.form.enable_auto_detect_visitor_currency'),
                'value' => setting('real_estate_enable_auto_detect_visitor_currency', false),
                'help_block' => [
                    'text' => trans('plugins/real-estate::settings.currency.form.auto_detect_visitor_currency_description'),
                ],
            ])
            ->add('real_estate_add_space_between_price_and_currency', 'onOffCheckbox', [
                'label' => trans('plugins/real-estate::settings.currency.form.add_space_between_price_and_currency'),
                'value' => setting('real_estate_add_space_between_price_and_currency', false),
            ])
            ->add('real_estate_thousands_separator', 'customSelect', [
                'label' => trans('plugins/real-estate::settings.currency.form.thousands_separator'),
                'choices' => [
                    ',' => trans('plugins/real-estate::settings.currency.form.separator_comma'),
                    '.' => trans('plugins/real-estate::settings.currency.form.separator_period'),
                    'space' => trans('plugins/real-estate::settings.currency.form.separator_space'),
                ],
                'selected' => setting('real_estate_thousands_separator', '.'),
            ])
            ->add('real_estate_decimal_separator', 'customSelect', [
                'label' => trans('plugins/real-estate::settings.currency.form.decimal_separator'),
                'choices' => [
                    '.' => trans('plugins/real-estate::settings.currency.form.separator_period'),
                    ',' => trans('plugins/real-estate::settings.currency.form.separator_comma'),
                    'space' => trans('plugins/real-estate::settings.currency.form.separator_space'),
                ],
                'selected' => setting('real_estate_decimal_separator', ','),
            ])
            ->add('currency-table', 'html', [
                'html' => view('plugins/real-estate::settings.partials.currency-table', compact('currencies'))->render(),
            ]);
    }
}
