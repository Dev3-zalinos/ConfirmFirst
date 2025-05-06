<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\RealEstate\Forms\FacilityForm;
use Botble\RealEstate\Http\Requests\FacilityRequest;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\RealEstate\Tables\FacilityTable;
use Exception;
use Illuminate\Http\Request;

class FacilityController extends BaseController
{
    public function __construct(protected FacilityInterface $facilityRepository)
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::facility.name'), route('facility.index'));
    }

    public function index(FacilityTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::facility.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::facility.create'));

        return FacilityForm::create()->renderForm();
    }

    public function store(FacilityRequest $request)
    {
        $facility = Facility::query()->create($request->input());

        event(new CreatedContentEvent(FACILITY_MODULE_SCREEN_NAME, $request, $facility));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('facility.index'))
            ->setNextUrl(route('facility.edit', $facility->id))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id, Request $request)
    {
        /**
         * @var Facility $facility
         */
        $facility = Facility::query()->findOrFail($id);

        event(new BeforeEditContentEvent($request, $facility));

        $this->pageTitle(trans('plugins/real-estate::facility.edit') . ' "' . $facility->name . '"');

        return FacilityForm::createFromModel($facility)->renderForm();
    }

    public function update(int|string $id, FacilityRequest $request)
    {
        $facility = Facility::query()->findOrFail($id);

        $facility->fill($request->input());
        $facility->save();

        event(new UpdatedContentEvent(FACILITY_MODULE_SCREEN_NAME, $request, $facility));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('facility.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $facility = Facility::query()->findOrFail($id);

            $facility->delete();

            event(new DeletedContentEvent(FACILITY_MODULE_SCREEN_NAME, $request, $facility));

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
