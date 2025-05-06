<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Feature;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class FeatureTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Feature::class)
            ->addActions([
                EditAction::make()->route('property_feature.edit'),
                DeleteAction::make()->route('property_feature.destroy'),
            ]);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'name',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('property_feature.edit'),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('property_feature.create'), 'property_feature.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('property_feature.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            NameBulkChange::make(),
        ];
    }
}
