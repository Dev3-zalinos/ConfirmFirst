<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Forms\PackageForm;
use Botble\RealEstate\Http\Requests\PackageRequest;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\RealEstate\Tables\PackageTable;
use Exception;
use Illuminate\Http\Request;

class PackageController extends BaseController
{
    public function __construct(protected PackageInterface $packageRepository)
    {
    }

    public function index(PackageTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::package.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::package.create'));

        return PackageForm::create()->renderForm();
    }

    public function store(PackageRequest $request)
    {
        $package = Package::query()->create($request->input());

        event(new CreatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('package.index'))
            ->setNextUrl(route('package.edit', $package->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, Request $request)
    {
        /**
         * @var Package $package
         */
        $package = Package::query()->findOrFail($id);

        event(new BeforeEditContentEvent($request, $package));

        $this->pageTitle(trans('plugins/real-estate::package.edit') . ' "' . $package->name . '"');

        return PackageForm::createFromModel($package)->renderForm();
    }

    public function update(int|string $id, PackageRequest $request)
    {
        $package = Package::query()->findOrFail($id);

        $package->fill($request->input());
        $package->save();

        event(new UpdatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('package.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $package = Package::query()->findOrFail($id);

            $package->delete();

            event(new DeletedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));

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
