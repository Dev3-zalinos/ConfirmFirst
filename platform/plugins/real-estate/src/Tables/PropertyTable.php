<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Payment\Models\Payment;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\IsFeaturedBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class PropertyTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Property::class)
            ->addActions([
                EditAction::make()->route('property.edit'),
                DeleteAction::make()->route('property.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('views', function (Property $item) {
                return number_format($item->views);
            })
            ->editColumn('unique_id', function (Property $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            })
            ->filter(function ($query) {
                if ($keyword = $this->request->input('search.value')) {
                    $keyword = '%' . $keyword . '%';

                    return $query
                        ->where('name', 'LIKE', $keyword)
                        ->orWhere('unique_id', 'LIKE', $keyword)
                        ->orWhere('location', 'LIKE', $keyword)
                        ->orWhereHas('city', function ($query) use ($keyword): void {
                            $query->where('name', 'LIKE', $keyword);
                        })
                        ->orWhereHas('state', function ($query) use ($keyword): void {
                            $query->where('name', 'LIKE', $keyword);
                        })
                        ->orWhereHas('country', function ($query) use ($keyword): void {
                            $query->where('name', 'LIKE', $keyword);
                        });
                }

                return $query;
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
                'name',
                'images',
                'views',
                'status',
                'moderation_status',
                'created_at',
                'unique_id',
                'location',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            ImageColumn::make()
                ->searchable(false)
                ->orderable(false),
            NameColumn::make()->route('property.edit'),
            Column::make('views')
                ->title(trans('plugins/real-estate::property.views')),
            Column::make('unique_id')
                ->title(trans('plugins/real-estate::property.unique_id')),
            CreatedAtColumn::make(),
            StatusColumn::make(),
            EnumColumn::make('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->width(150),
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('property.create'), 'property.create');

        if ($this->hasPermission('import-properties.index')) {
            $buttons['import'] = [
                'link' => route('properties.import.index'),
                'text' =>
                    BaseHelper::renderIcon('ti ti-upload')
                    . trans('plugins/real-estate::property.import_properties'),
            ];
        }

        if ($this->hasPermission('export-properties.index')) {
            $buttons['export'] = [
                'link' => route('export-properties.index'),
                'text' =>
                    BaseHelper::renderIcon('ti ti-download')
                    . trans('plugins/real-estate::property.export_properties'),
            ];
        }

        return $buttons;
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('property.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            NameBulkChange::make(),
            StatusBulkChange::make()->choices(PropertyStatusEnum::labels()),
            StatusBulkChange::make()
                ->name('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->choices(ModerationStatusEnum::labels()),
            SelectBulkChange::make()
                ->name('project_id')
                ->title(trans('plugins/real-estate::property.form.project'))
                ->searchable()
                ->choices(fn () => Project::query()->pluck('name', 'id')->all()),
            CreatedAtBulkChange::make(),
            IsFeaturedBulkChange::make(),
        ];
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, ?string $value): EloquentRelation|EloquentBuilder|QueryBuilder
    {
        if ($key == 'status') {
            switch ($value) {
                case 'expired':
                    // @phpstan-ignore-next-line
                    return $query->expired();
                case 'active':
                    // @phpstan-ignore-next-line
                    return $query->active();
            }
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    public function saveBulkChangeItem(Model|Payment $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'moderation_status') {
            $item->moderation_status = $inputValue;

            $item->save();
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
