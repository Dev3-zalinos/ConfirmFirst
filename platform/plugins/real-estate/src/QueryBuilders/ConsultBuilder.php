<?php

namespace Botble\RealEstate\QueryBuilders;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\RealEstate\Models\Account;
use Illuminate\Database\Eloquent\Builder;

class ConsultBuilder extends BaseQueryBuilder
{
    public function whereAccount(Account $account): static
    {
        return $this
            ->whereHas('project', function (Builder $query) use ($account): void {
                $query
                    ->where('author_type', $account::class)
                    ->where('author_id', $account->id);
            })
            ->orWhereHas('property', function (Builder $query) use ($account): void {
                $query
                    ->where('author_type', $account::class)
                    ->where('author_id', $account->id);
            });
    }
}
