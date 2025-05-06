<?php

namespace Botble\RealEstate\Tables\Fronts;

use Botble\RealEstate\QueryBuilders\ConsultBuilder;
use Botble\RealEstate\Tables\ConsultTable as BaseConsultTable;
use Botble\Table\Actions\Action;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;

class ConsultTable extends BaseConsultTable
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setView('plugins/real-estate::account.table.base')
            ->modifyQueryUsing(fn (ConsultBuilder $query) => $query->whereAccount(auth('account')->user()))
            ->addAction(
                Action::make('view')
                    ->route('public.account.consults.show')
                    ->icon('ti ti-eye')
                    ->label(trans('core/base::tables.view'))
            )
            ->removeActions(['edit', 'delete']);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('public.account.consults.show'),
            EmailColumn::make(),
            Column::make('phone')
                ->title(trans('plugins/real-estate::consult.phone')),
            CreatedAtColumn::make(),
        ];
    }
}
