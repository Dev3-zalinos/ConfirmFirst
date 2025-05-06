<?php

namespace Theme\Homzen\Actions;

use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GetPropertiesAction
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<\Botble\RealEstate\Models\Property|\Illuminate\Database\Eloquent\Model>
     */
    public function handle(
        ?int $limit = 4,
        ?string $categoryId = null,
        ?string $type = null,
        bool $featured = false,
        array $categoryIds = []
    ): Collection {
        return Property::query()
            ->where(RealEstateHelper::getPropertyDisplayQueryConditions())
            ->when(
                $featured,
                fn (Builder $query, string $type) => $query->where('is_featured', $featured) // @phpstan-ignore-line
            )
            ->when(
                $type,
                fn (Builder $query, string $type) => $query->where('type', $type)
            )
            ->when(
                $categoryId,
                fn (Builder $query, string $categoryId) => $query->whereRelation('categories', 'id', $categoryId)
            )
            ->when($categoryIds, function (Builder $query, array $categoryIds) {
                return $query->whereHas('categories', fn (Builder $query) => $query->whereIn('id', $categoryIds));
            })
            ->take($limit)
            ->orderByDesc('is_featured')
            ->orderByDesc('featured_priority')
            ->latest()
            ->with([...RealEstateHelper::getPropertyRelationsQuery(), 'author'])
            ->get();
    }
}
