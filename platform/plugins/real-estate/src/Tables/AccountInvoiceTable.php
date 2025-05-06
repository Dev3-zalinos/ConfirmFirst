<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Invoice;
use Botble\Table\Actions\Action;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class AccountInvoiceTable extends InvoiceTable
{
    public function setup(): void
    {
        $this
            ->model(Invoice::class)
            ->addActions([
                Action::make('analytics')
                    ->route('public.account.invoices.show')
                    ->icon('ti ti-eye')
                    ->label(__('View'))
                    ->color('info'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('amount', function (Invoice $item) {
                return format_price($item->amount);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'code',
                'amount',
                'created_at',
                'status',
            ])
            ->where('account_id', auth('account')->id());

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            LinkableColumn::make('code')
                ->title(trans('plugins/real-estate::invoice.code'))
                ->route('public.account.invoices.show')
                ->alignLeft(),
            Column::make('amount')
                ->title(trans('plugins/real-estate::invoice.amount'))
                ->alignLeft(),
            CreatedAtColumn::make(),
            StatusColumn::make(),
        ];
    }
}
