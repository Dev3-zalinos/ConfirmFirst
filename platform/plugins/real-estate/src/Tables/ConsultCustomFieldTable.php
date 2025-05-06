<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\ConsultCustomField;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class ConsultCustomFieldTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(ConsultCustomField::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('consult.custom-fields.create')->permission('consult.edit'))
            ->addBulkChanges([
                NameBulkChange::make()->validate('required|max:120'),
                CreatedAtBulkChange::make(),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('consult.edit'))
            ->addActions([
                EditAction::make()->route('consult.custom-fields.edit')->permission('consult.edit'),
                DeleteAction::make()->route('consult.custom-fields.destroy')->permission('consult.edit'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('consult.custom-fields.edit')->permission('consult.edit'),
                EnumColumn::make('type')
                    ->title(trans('plugins/real-estate::consult.custom_field.type'))
                    ->alignLeft(),
                CreatedAtColumn::make(),
            ])
            ->queryUsing(fn (Builder $query) => $query->select([
                'id',
                'name',
                'type',
                'created_at',
            ]));
    }
}
