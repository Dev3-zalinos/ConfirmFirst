<?php

namespace Botble\RealEstate\Tables;

use Botble\Table\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class UnverifiedAccountTable extends AccountTable
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->removeActions(['edit', 'delete'])
            ->removeHeaderAction('create')
            ->addAction(
                Action::make('moderate')
                    ->icon('ti ti-eye')
                    ->permission('account.edit')
                    ->url(fn (Action $action) => route('unverified-accounts.show', $action->getItem()))
                    ->color('primary')
                    ->label(trans('plugins/real-estate::account.moderate'))
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
                    ])
                    ->with(['avatar'])
                    ->withCount(['properties'])
                    ->whereNull('approved_at');
            });
    }
}
