<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\RealEstate\Forms\InvestorForm;
use Botble\RealEstate\Http\Requests\InvestorRequest;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Repositories\Interfaces\InvestorInterface;
use Botble\RealEstate\Tables\InvestorTable;
use Exception;
use Illuminate\Http\Request;

class InvestorController extends BaseController
{
    public function __construct(protected InvestorInterface $investorRepository)
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::investor.name'), route('investor.index'));
    }

    public function index(InvestorTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::investor.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::investor.create'));

        return InvestorForm::create()->renderForm();
    }

    public function store(InvestorRequest $request)
    {
        $investor = Investor::query()->create($request->input());

        event(new CreatedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('investor.index'))
            ->setNextUrl(route('investor.edit', $investor->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, Request $request)
    {
        /**
         * @var Investor $investor
         */
        $investor = Investor::query()->findOrFail($id);

        event(new BeforeEditContentEvent($request, $investor));

        $this->pageTitle(trans('plugins/real-estate::investor.edit') . ' "' . $investor->name . '"');

        return InvestorForm::createFromModel($investor)->renderForm();
    }

    public function update(int|string $id, InvestorRequest $request)
    {
        $investor = Investor::query()->findOrFail($id);

        $investor->fill($request->input());
        $investor->save();

        event(new UpdatedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('investor.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $investor = Investor::query()->findOrFail($id);

            $investor->delete();

            event(new DeletedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));

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
