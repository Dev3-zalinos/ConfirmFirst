<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\RealEstate\Forms\Settings\InvoiceSettingForm;
use Botble\RealEstate\Http\Requests\Settings\InvoiceSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class InvoiceSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.invoice.name'));

        return InvoiceSettingForm::create()->renderForm();
    }

    public function update(InvoiceSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
