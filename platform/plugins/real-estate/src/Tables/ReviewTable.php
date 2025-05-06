<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Models\Review;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Support\Facades\DB;

class ReviewTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Review::class)
            ->addActions([
                DeleteAction::make()->route('review.destroy'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('review.destroy'),
            ])
            ->addBulkChanges([
                StatusBulkChange::make()->choices(ReviewStatusEnum::labels()),
                CreatedAtBulkChange::make(),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('account_id')
                    ->title(trans('plugins/real-estate::review.author'))
                    ->alignLeft()
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    if (! $item->account_id || ! $item->author?->id) {
                        return '&mdash;';
                    }

                    return Html::link(route('account.edit', $item->author->id), BaseHelper::clean($item->author->name))->toHtml();
                }),
                FormattedColumn::make('reviewable')
                    ->title(trans('plugins/real-estate::review.reviewable'))
                    ->alignLeft()
                    ->orderable(false)
                    ->searchable(false)
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    if (! $item->reviewable_id || ! $item->reviewable?->getKey()) {
                        return '&mdash;';
                    }

                    return Html::link($item->reviewable->url, $item->reviewable->name, ['target' => '_blank']);
                }),
                FormattedColumn::make('star')
                    ->title(trans('plugins/real-estate::review.star'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return view('plugins/real-estate::partials.review-star', ['star' => $item->star])->render();
                    }),
                FormattedColumn::make('content')
                    ->title(trans('plugins/real-estate::review.content'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return BaseHelper::clean($item->content);
                    }),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->queryUsing(function ($query) {
                return $query
                    ->select([
                        'id',
                        'reviewable_type',
                        'reviewable_id',
                        'star',
                        'content',
                        'account_id',
                        'status',
                        'created_at',
                    ])
                    ->with(['author', 'reviewable']);
            })
            ->onAjax(function (self $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                        ->filter(function ($query) {
                            $keyword = $this->request->input('search.value');

                            if ($keyword) {
                                return $query
                                    ->where('content', 'LIKE', '%' . $keyword . '%')
                                    ->orWhereHas('reviewable', function ($subQuery) use ($keyword) {
                                        return $subQuery->where('name', 'LIKE', '%' . $keyword . '%');
                                    })
                                    ->orWhereHas('author', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('first_name', 'LIKE', '%' . $keyword . '%')
                                            ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                                            ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $keyword . '%');
                                    });
                            }

                            return $query;
                        })
                );
            });
    }
}
