<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Forms\ConsultForm;
use Botble\RealEstate\Http\Requests\ConsultRequest;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Tables\ConsultTable;

class ConsultController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::consult.name'), route('consult.index'));
    }

    public function index(ConsultTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::consult.name'));

        return $table->renderTable();
    }

    public function edit(Consult $consult)
    {
        $consult->load(['project', 'property']);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $consult->name]));

        return ConsultForm::createFromModel($consult)->renderForm();
    }

    public function update(Consult $consult, ConsultRequest $request)
    {
        ConsultForm::createFromModel($consult)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('consult.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Consult $consult): DeleteResourceAction
    {
        return DeleteResourceAction::make($consult);
    }
}
