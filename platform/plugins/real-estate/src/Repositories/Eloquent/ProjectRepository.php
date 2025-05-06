<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\Language\Facades\Language;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ProjectRepository extends RepositoriesAbstract implements ProjectInterface
{
    public function getProjects($filters = [], $params = []): Collection|LengthAwarePaginator
    {
        $filters = array_merge([
            'keyword' => null,
            'min_floor' => null,
            'max_floor' => null,
            'blocks' => null,
            'min_flat' => null,
            'max_flat' => null,
            'category_id' => null,
            'city_id' => null,
            'city' => null,
            'min_price' => null,
            'max_price' => null,
            'state' => null,
            'state_id' => null,
            'location' => null,
            'sort_by' => null,
            'features' => null,
        ], $filters);

        $orderBy = match ($filters['sort_by']) {
            'date_asc' => [
                'created_at' => 'ASC',
            ],
            'price_asc' => [
                'price_from' => 'ASC',
            ],
            'price_desc' => [
                'price_from' => 'DESC',
            ],
            'name_asc' => [
                'name' => 'ASC',
            ],
            'name_desc' => [
                'name' => 'DESC',
            ],
            default => [
                'created_at' => 'DESC',
            ],
        };

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                '*',
            ],
            'with' => [],
        ], $params);

        // Initialize the model with active projects
        $this->model = $this->originalModel->active(); // @phpstan-ignore-line

        // Always sort by featured status first, then by featured_priority, then by the regular ordering
        $this->model = $this->model
            // First sort by featured status (featured projects first)
            ->orderByDesc('is_featured')
            // Then sort featured projects by their priority (higher values first)
            ->orderByDesc('featured_priority');

        // Then apply the regular ordering for projects with same featured status and priority
        foreach ($orderBy as $column => $direction) {
            $this->model = $this->model->orderBy($column, $direction);
        }

        if ($filters['keyword'] !== null) {
            $keyword = $filters['keyword'];

            $this->model = $this->model
                ->where(function (BaseQueryBuilder $query) use ($keyword) {
                    return $query
                        ->addSearch('name', $keyword, false, false)
                        ->addSearch('location', $keyword, false)
                        ->addSearch('description', $keyword, false)
                        ->addSearch('unique_id', $keyword, false);
                });
        }

        if ($filters['city'] !== null) {
            $this->model = $this->model->whereHas('city', function ($query) use ($filters): void {
                $query->where('slug', $filters['city']);
            });
        }

        if ($filters['state'] !== null) {
            $this->model = $this->model->whereHas('state', function ($query) use ($filters): void {
                $query->where('slug', $filters['state']);
            });
        }

        if ($filters['blocks']) {
            if ($filters['blocks'] < 5) {
                $this->model = $this->model->where('number_block', $filters['blocks']);
            } else {
                $this->model = $this->model->where('number_block', '>=', $filters['blocks']);
            }
        }

        if ($filters['min_floor'] !== null || $filters['max_floor'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minFloor = Arr::get($filters, 'min_floor');
                    $maxFloor = Arr::get($filters, 'max_floor');

                    if ($minFloor !== null) {
                        $query = $query->where('number_floor', '>=', $minFloor);
                    }

                    if ($maxFloor !== null) {
                        $query = $query->where('number_floor', '<=', $maxFloor);
                    }

                    return $query;
                });
        }

        if ($filters['min_flat'] !== null || $filters['max_flat'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minFlat = Arr::get($filters, 'min_flat');
                    $maxFlat = Arr::get($filters, 'max_flat');

                    if ($minFlat !== null) {
                        $query = $query->where('number_flat', '>=', $minFlat);
                    }

                    if ($maxFlat !== null) {
                        $query = $query->where('number_flat', '<=', $maxFlat);
                    }

                    return $query;
                });
        }

        if ($filters['category_id'] !== null) {
            $categoryIds = get_property_categories_related_ids($filters['category_id']);
            $this->model = $this->model
                ->whereHas('categories', function (Builder $query) use ($categoryIds): void {
                    $query->whereIn('category_id', $categoryIds);
                });
        }

        if ($filters['state_id']) {
            $this->model = $this->model->where('state_id', $filters['state_id']);
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('city_id', $filters['city_id']);
        } elseif ($filters['location']) {
            $locationData = explode(',', $filters['location']);

            if (count($locationData) > 1) {
                $locationSearch = trim($locationData[0]);
            } else {
                $locationSearch = trim($filters['location']);
            }

            if (is_plugin_active('language') && is_plugin_active('language-advanced') && Language::getCurrentLocale() != Language::getDefaultLocale()) {
                $this->model = $this->model
                    ->where(function (BaseQueryBuilder $query) use ($locationSearch) {
                        return $query
                            ->whereHas('translations', function (BaseQueryBuilder $query) use ($locationSearch): void {
                                $query->addSearch('location', $locationSearch, false, false);
                            })
                            ->orWhereHas('city.translations', function (BaseQueryBuilder $query) use ($locationSearch): void {
                                $query->addSearch('name', $locationSearch, false, false);
                            })
                            ->orWhereHas('state.translations', function (BaseQueryBuilder $query) use ($locationSearch): void {
                                $query->addSearch('name', $locationSearch, false, false);
                            });
                    });
            } else {
                $this->model = $this->model
                    ->where(function ($query) use ($locationSearch) {
                        return $query
                            ->addSearch('location', $locationSearch, false, false)
                            ->orWhereHas('city', function (BaseQueryBuilder $query) use ($locationSearch): void {
                                $query->addSearch('cities.name', $locationSearch, false, false);
                            })
                            ->orWhereHas('state', function (BaseQueryBuilder $query) use ($locationSearch): void {
                                $query->addSearch('states.name', $locationSearch, false, false);
                            });
                    });
            }
        }

        if (count($filters['category_ids'] ?? [])) {
            $categoryIds = $filters['category_ids'];

            $this->model = $this->model
                ->whereHas('categories', function (Builder $query) use ($categoryIds): void {
                    $query->whereIn('category_id', $categoryIds);
                });
        }

        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $minPrice = Arr::get($filters, 'min_price');
            $maxPrice = Arr::get($filters, 'max_price');

            if ($minPrice && $minPrice > 0) {
                $this->model = $this->model
                    ->where(function ($query) use ($minPrice): void {
                        $query
                            ->whereNull('price_from')
                            ->orWhere('price_from', '>=', $minPrice);
                    });
            }

            if ($maxPrice && $maxPrice > 0) {
                $this->model = $this->model
                    ->where(function ($query) use ($maxPrice): void {
                        $query
                            ->whereNull('price_to')
                            ->orWhere('price_to', '<=', $maxPrice);
                    });
            }
        }

        if ($filters['locations'] ?? []) {
            $locationsSearch = $filters['locations'];

            if (is_plugin_active('language') && is_plugin_active('language-advanced') && Language::getCurrentLocale() != Language::getDefaultLocale()) {
                $this->model = $this->model
                    ->where(function (BaseQueryBuilder $query) use ($locationsSearch) {
                        return $query
                            ->whereHas('translations', function (BaseQueryBuilder $query) use ($locationsSearch): void {
                                foreach ($locationsSearch as $location) {
                                    $query->addSearch('location', $location, false);
                                }
                            })
                            ->orWhereHas('city.translations', function (BaseQueryBuilder $query) use ($locationsSearch): void {
                                foreach ($locationsSearch as $location) {
                                    $query->addSearch('name', $location, false);
                                }
                            })
                            ->orWhereHas('state.translations', function (BaseQueryBuilder $query) use ($locationsSearch): void {
                                foreach ($locationsSearch as $location) {
                                    $query->addSearch('name', $location, false);
                                }
                            });
                    });
            } else {
                $this->model = $this->model
                    ->where(function (BaseQueryBuilder $query) use ($locationsSearch) {
                        return $query
                            ->where(function (BaseQueryBuilder $query) use ($locationsSearch): void {
                                foreach ($locationsSearch as $location) {
                                    $query->addSearch('location', $location, false);
                                }
                            })
                            ->orWhereHas('city', function (BaseQueryBuilder $query) use ($locationsSearch): void {
                                foreach ($locationsSearch as $location) {
                                    $query->addSearch('cities.name', $location, false);
                                }
                            })
                            ->orWhereHas('state', function (BaseQueryBuilder $query) use ($locationsSearch): void {
                                foreach ($locationsSearch as $location) {
                                    $query->addSearch('states.name', $location, false);
                                }
                            });
                    });
            }
        }

        if ($filters['features'] !== null) {
            $features = array_filter((array) $filters['features']);

            if ($features) {
                $projectIds = $this
                    ->getModel()
                    ->toBase()
                    ->select('re_projects.id')
                    ->join('re_project_features', 're_projects.id', '=', 're_project_features.project_id')
                    ->whereIn('re_project_features.feature_id', $features)
                    ->groupBy('re_projects.id')
                    ->havingRaw('COUNT(DISTINCT re_project_features.feature_id) = ' . count($features))
                    ->pluck('re_projects.id')
                    ->all();

                $this->model = $this->model->whereIn('id', $projectIds);
            }
        }

        $this->model = apply_filters('projects_filter_query', $this->model, $filters, $params);

        return $this->advancedGet($params);
    }

    public function getRelatedProjects(int $projectId, int $limit = 4, array $with = []): Collection|LengthAwarePaginator
    {
        $currentProject = $this->findById($projectId, ['categories']);

        $this->model = $this->originalModel;
        $this->model = $this->model->active()->whereNot('id', $projectId); // @phpstan-ignore-line

        if ($currentProject && $currentProject->categories->count()) {
            $categoryIds = $currentProject->categories->pluck('id')->toArray();

            $this->model
                ->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('re_project_categories.category_id', $categoryIds);
                });
        }

        $params = [
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
            'with' => $with,
        ];

        return $this->advancedGet($params);
    }
}
