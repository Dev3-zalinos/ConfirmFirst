<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\Invoice;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\ViewAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;

class InvoiceTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Invoice::class)
            ->addActions([
                ViewAction::make()
                    ->route('invoices.show')
                    ->permission('invoices.edit'),
                DeleteAction::make()->route('invoices.destroy'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('invoices.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()
                    ->name('account_id')
                    ->title(trans('plugins/real-estate::invoice.account')),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('account_id')
                    ->title(trans('plugins/real-estate::invoice.account'))
                    ->alignLeft()
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        if (! $item->account) {
                            return null;
                        }

                        return Html::link(route('account.edit', $item->account), $item->account->name);
                    })
                    ->withEmptyState(),
                LinkableColumn::make('code')
                    ->title(trans('plugins/real-estate::invoice.code'))
                    ->route('invoices.show')
                    ->permission('invoices.edit')
                    ->alignLeft(),
                FormattedColumn::make('amount')
                    ->title(trans('plugins/real-estate::invoice.amount'))
                    ->alignLeft()
                    ->getValueUsing(function (FormattedColumn $column) {
                        return format_price($column->getItem()->amount);
                    }),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select([
                        'id',
                        'account_id',
                        'code',
                        'amount',
                        'created_at',
                        'status',
                    ])
                    ->with('account');
            });
    }
}
