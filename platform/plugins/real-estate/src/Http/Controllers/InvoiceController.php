<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\RealEstate\Models\Invoice;
use Botble\RealEstate\Repositories\Interfaces\InvoiceInterface;
use Botble\RealEstate\Supports\InvoiceHelper;
use Botble\RealEstate\Tables\InvoiceTable;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function __construct(protected InvoiceInterface $invoiceRepository)
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::invoice.name'), route('invoices.index'));
    }

    public function index(InvoiceTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::invoice.name'));

        return $table->renderTable();
    }

    public function show(int|string $id)
    {
        $invoice = Invoice::query()->findOrFail($id);

        $this->pageTitle(trans('plugins/real-estate::invoice.show', ['code' => $invoice->code]));

        return view('plugins/real-estate::invoices.show', compact('invoice'));
    }

    public function generate(int|string $id, Request $request, InvoiceHelper $invoiceHelper)
    {
        /**
         * @var Invoice $invoice
         */
        $invoice = Invoice::query()->findOrFail($id);

        if ($request->input('type') === 'print') {
            return $invoiceHelper->streamInvoice($invoice);
        }

        return $invoiceHelper->downloadInvoice($invoice);
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $invoice = Invoice::query()->findOrFail($id);

            $invoice->delete();

            event(new DeletedContentEvent(INVOICE_MODULE_SCREEN_NAME, $request, $invoice));

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
