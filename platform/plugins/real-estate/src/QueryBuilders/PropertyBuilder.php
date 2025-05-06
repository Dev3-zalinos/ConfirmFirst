<?php

namespace Botble\RealEstate\QueryBuilders;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\RealEstate\Facades\RealEstateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PropertyBuilder extends BaseQueryBuilder
{
    public function notExpired(): static
    {
        $this->where(function (Builder $query): void {
            $query
                ->where('expire_date', '>=', Carbon::now()->toDateTimeString())
                ->orWhere('never_expired', true);
        });

        return $this;
    }

    public function expired(): static
    {
        $this->where(function (Builder $query): void {
            $query
                ->where('expire_date', '<', Carbon::now()->toDateTimeString())
                ->where('never_expired', false);
        });

        return $this;
    }

    public function active(): static
    {
        $this
            ->where(RealEstateHelper::getPropertyDisplayQueryConditions())
            ->notExpired();

        return $this;
    }
}
