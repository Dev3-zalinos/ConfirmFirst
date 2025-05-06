<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class AccountPropertyTable extends PropertyTable
{
    public function setup(): void
    {
        $this
            ->model(Property::class)
            ->addActions([
                Action::make('renew')
                    ->route('public.account.properties.renew')
                    ->icon('ti ti-refresh')
                    ->label(__('Renew'))
                    ->color('info')
                    ->attributes([
                        'data-bb-toggle' => 'property-renew-modal',
                    ]),
                EditAction::make()->route('public.account.properties.edit'),
                DeleteAction::make()->route('public.account.properties.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('unique_id', function (Property $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            })
            ->editColumn('expire_date', function (Property $item) {
                if ($item->never_expired) {
                    return trans('plugins/real-estate::property.never_expired_label');
                }

                if (! $item->expire_date) {
                    return '&mdash;';
                }

                if ($item->expire_date->isPast()) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-danger'])->toHtml();
                }

                if (Carbon::now()->diffInDays($item->expire_date) < 3) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-warning'])->toHtml();
                }

                return $item->expire_date->toDateString();
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
                'created_at',
                'status',
                'moderation_status',
                'expire_date',
                'views',
                'unique_id',
            ])
            ->where([
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]);

        return $this->applyScopes($query);
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function getBulkChanges(): array
    {
        return [];
    }

    public function hasBulkActions(): bool
    {
        return false;
    }

    public function buttons(): array
    {
        $buttons = [];
        if (auth('account')->user()->canPost()) {
            $buttons = $this->addCreateButton(route('public.account.properties.create'));
        }

        return $buttons;
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            ImageColumn::make()
                ->searchable(false)
                ->orderable(false),
            NameColumn::make()->route('public.account.properties.edit'),
            Column::make('views')
                ->title(trans('plugins/real-estate::property.views')),
            Column::make('unique_id')
                ->title(trans('plugins/real-estate::property.unique_id')),
            Column::make('expire_date')
                ->title(trans('plugins/real-estate::property.expire_date'))
                ->width(150),
            CreatedAtColumn::make(),
            StatusColumn::make(),
            EnumColumn::make('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->width(150),
        ];
    }
}
