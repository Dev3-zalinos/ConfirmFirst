<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\RealEstate\Forms\ConsultCustomFieldForm;
use Botble\RealEstate\Http\Requests\ConsultCustomFieldRequest;
use Botble\RealEstate\Models\ConsultCustomField;
use Botble\RealEstate\Tables\ConsultCustomFieldTable;

class ConsultCustomFieldController extends BaseController
{
    public function index(ConsultCustomFieldTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::consult.custom_field.name'));

        return $table->renderTable();
    }

    public function create(): string
    {
        $this->pageTitle(trans('plugins/real-estate::consult.custom_field.create'));

        return ConsultCustomFieldForm::create()->renderForm();
    }

    public function store(ConsultCustomFieldRequest $request)
    {
        $form = ConsultCustomFieldForm::create();
        $form->setRequest($request)->saveOnlyValidatedData();

        if (! empty($options = $request->input('options', []))) {
            $form->getModel()->saveOptions($options);
        }

        return $this
            ->httpResponse()
            ->setNextUrl(route('consult.custom-fields.index'))
            ->withCreatedSuccessMessage();
    }

    public function edit(ConsultCustomField $customField): string
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $customField->name]));

        return ConsultCustomFieldForm::createFromModel($customField)->renderForm();
    }

    public function update(ConsultCustomField $customField, ConsultCustomFieldRequest $request)
    {
        ConsultCustomFieldForm::createFromModel($customField)->setRequest($request)->saveOnlyValidatedData();

        if (! empty($options = $request->input('options', []))) {
            $customField->saveOptions($options);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('consult.custom-fields.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(ConsultCustomField $customField)
    {
        return DeleteResourceAction::make($customField);
    }
}
