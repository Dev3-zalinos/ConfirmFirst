<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\RealEstate\Forms\Settings\AccountSettingForm;
use Botble\RealEstate\Http\Requests\Settings\AccountSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class AccountSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.account.name'));

        return AccountSettingForm::create()->renderForm();
    }

    public function update(AccountSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
