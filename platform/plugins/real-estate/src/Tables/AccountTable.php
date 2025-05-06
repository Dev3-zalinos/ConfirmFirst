<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Models\Account;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\EmailBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\YesNoColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class AccountTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Account::class)
            ->addActions([
                EditAction::make()->route('account.edit'),
                DeleteAction::make()->route('account.destroy'),
            ])
            ->addHeaderAction(
                CreateHeaderAction::make()
                    ->route('account.create')
                    ->permission('account.create')
            )
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select([
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'created_at',
                        'credits',
                        'avatar_id',
                        'confirmed_at',
                    ])
                    ->with(['avatar'])
                    ->withCount(['properties'])
                    ->when((bool) setting('real_estate_enable_account_verification', false), function (Builder $query): void {
                        $query->whereNotNull('approved_at');
                    });
            })
            ->addBulkActions([
                DeleteBulkAction::make()->permission('account.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()
                    ->name('first_name')
                    ->title(trans('plugins/real-estate::account.first_name'))
                    ->validate('required|max:120'),
                TextBulkChange::make()
                    ->name('last_name')
                    ->title(trans('plugins/real-estate::account.last_name'))
                    ->validate('required|max:120'),
                EmailBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('avatar_id')
                    ->title(trans('core/base::tables.image'))
                    ->width(70)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return Html::image(
                            RvMedia::getImageUrl($column->getItem()->avatar->url, 'thumb', false, RvMedia::getDefaultImage()),
                            BaseHelper::clean($column->getItem()->name),
                            ['width' => 50]
                        );
                    }),
                NameColumn::make()
                    ->route('account.edit')
                    ->orderable(false)
                    ->searchable(false),
                EmailColumn::make(),
                FormattedColumn::make('phone')
                    ->title(trans('plugins/real-estate::account.phone'))
                    ->alignLeft()
                    ->getValueUsing(function (FormattedColumn $column) {
                        return BaseHelper::clean($column->getItem()->phone ?: '&mdash;');
                    }),
                Column::make('credits')
                    ->title(trans('plugins/real-estate::account.credits'))
                    ->alignLeft(),
                FormattedColumn::make('updated_at')
                    ->title(trans('plugins/real-estate::account.number_of_properties'))
                    ->width(100)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->properties_count;
                    }),
                CreatedAtColumn::make(),
            ])
            ->onAjax(function (AccountTable $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                    ->filter(function (Builder $query) {
                        $keyword = $this->request->input('search.value');

                        if (! $keyword) {
                            return $query;
                        }

                        return $query->where(function (Builder $query) use ($keyword): void {
                            $query
                                ->where('id', $keyword)
                                ->orWhere('first_name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                                ->orWhereDate('created_at', $keyword);
                        });
                    })
                );
            });

        if (setting('verify_account_email', false)) {
            $this->addColumn(
                YesNoColumn::make('confirmed_at')
                    ->title(trans('plugins/real-estate::account.email_verified')),
            );
        }
    }
}
