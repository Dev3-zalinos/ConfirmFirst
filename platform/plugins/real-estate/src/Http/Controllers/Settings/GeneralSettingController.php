<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\RealEstate\Forms\Settings\GeneralSettingForm;
use Botble\RealEstate\Http\Requests\Settings\GeneralSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class GeneralSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.general.name'));

        return GeneralSettingForm::create()->renderForm();
    }

    public function update(GeneralSettingRequest $request)
    {
        return $this->performUpdate([
            ...$request->validated(),
            'real_estate_hide_properties_in_statuses' => $request->input('real_estate_hide_properties_in_statuses', []),
            'real_estate_hide_projects_in_statuses' => $request->input('real_estate_hide_projects_in_statuses', []),
            'real_estate_mandatory_fields_at_consult_form' => $request->input('real_estate_mandatory_fields_at_consult_form', []),
            'real_estate_hide_fields_at_consult_form' => $request->input('real_estate_hide_fields_at_consult_form', []),
        ]);
    }
}
