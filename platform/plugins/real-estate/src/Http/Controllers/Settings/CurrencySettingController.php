<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\RealEstate\Forms\Settings\CurrencySettingForm;
use Botble\RealEstate\Http\Requests\Settings\CurrencySettingRequest;
use Botble\RealEstate\Services\StoreCurrenciesService;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Support\Arr;

class CurrencySettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.currency.name'));

        $form = CurrencySettingForm::create();

        return view('plugins/real-estate::settings.currency', compact('form'));
    }

    public function update(
        CurrencySettingRequest $request,
        StoreCurrenciesService $service,
    ) {
        $this->saveSettings(Arr::except($request->validated(), [
            'currencies',
            'currencies_data',
            'deleted_currencies',
        ]));

        $currencies = json_decode($request->validated('currencies'), true) ?: [];

        if (! $currencies) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('real-estate.settings'))
                ->setError()
                ->setMessage(trans('plugins/real-estate::currency.require_at_least_one_currency'));
        }

        $deletedCurrencies = json_decode($request->input('deleted_currencies', []), true) ?: [];

        $storedCurrencies = $service->execute($currencies, $deletedCurrencies);

        if ($storedCurrencies['error']) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($storedCurrencies['message']);
        }

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }
}
