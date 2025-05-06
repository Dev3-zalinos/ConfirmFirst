<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\CustomFieldForm;
use Botble\RealEstate\Http\Requests\CustomFieldRequest;
use Botble\RealEstate\Http\Resources\CustomFieldResource;
use Botble\RealEstate\Models\CustomField;
use Botble\RealEstate\Tables\CustomFieldTable;
use Closure;
use Illuminate\Http\Request;

class CustomFieldController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            abort_unless(RealEstateHelper::isEnabledCustomFields(), 404);

            return $next($request);
        });
    }

    public function index(CustomFieldTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::custom-fields.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::custom-fields.create'));

        return CustomFieldForm::create()->renderForm();
    }

    public function store(CustomFieldRequest $request)
    {
        $customField = new CustomField();
        $customField->fill($request->validated());
        $customField->authorable_type = User::class;
        $customField->authorable_id = auth()->id();
        $customField->save();

        $customField->saveRelations($request->validated());

        event(new CreatedContentEvent(REAL_ESTATE_CUSTOM_FIELD_MODULE_SCREEN_NAME, $request, $customField));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('real-estate.custom-fields.index'))
            ->setNextUrl(route('real-estate.custom-fields.edit', $customField->id))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id, Request $request)
    {
        /**
         * @var CustomField $customField
         */
        $customField = CustomField::query()->with(['options'])->findOrFail($id);

        event(new BeforeEditContentEvent($request, $customField));

        $this->pageTitle(trans('plugins/real-estate::custom-fields.edit', ['name' => $customField->name]));

        return CustomFieldForm::createFromModel($customField)->renderForm();
    }

    public function update(int|string $id, CustomFieldRequest $request)
    {
        /**
         * @var CustomField $customField
         */
        $customField = CustomField::query()->findOrFail($id);

        $customField->fill($request->validated());
        $customField->save();

        $customField->saveRelations($request->validated());

        event(new UpdatedContentEvent(REAL_ESTATE_CUSTOM_FIELD_MODULE_SCREEN_NAME, $request, $customField));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('real-estate.custom-fields.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(int|string $id)
    {
        $customField = CustomField::query()->findOrFail($id);

        return DeleteResourceAction::make($customField);
    }

    public function getInfo(Request $request)
    {
        $customField = CustomField::query()->with(['options'])->findOrFail($request->input('id'));

        return new CustomFieldResource($customField);
    }
}
