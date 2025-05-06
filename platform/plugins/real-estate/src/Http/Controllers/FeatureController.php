<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\RealEstate\Forms\FeatureForm;
use Botble\RealEstate\Http\Requests\FeatureRequest;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Tables\FeatureTable;
use Exception;
use Illuminate\Http\Request;

class FeatureController extends BaseController
{
    public function __construct(protected FeatureInterface $featureRepository)
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::feature.name'), route('property_feature.index'));
    }

    public function index(FeatureTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::feature.name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::feature.create'));

        return FeatureForm::create()->renderForm();
    }

    public function store(FeatureRequest $request)
    {
        $feature = $this->featureRepository->create($request->all());

        event(new CreatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property_feature.index'))
            ->setNextUrl(route('property_feature.edit', $feature->id))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id, Request $request)
    {
        /**
         * @var Feature $feature
         */
        $feature = Feature::query()->findOrFail($id);

        $this->pageTitle(trans('plugins/real-estate::feature.edit') . ' "' . $feature->name . '"');

        event(new BeforeEditContentEvent($request, $feature));

        return FeatureForm::createFromModel($feature)->renderForm();
    }

    public function update(int|string $id, FeatureRequest $request)
    {
        $feature = Feature::query()->findOrFail($id);

        $feature->fill($request->input());
        $feature->save();

        event(new UpdatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property_feature.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $feature = Feature::query()->findOrFail($id);
            $feature->delete();

            event(new DeletedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

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
