<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\Language\Facades\Language;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PropertyRepository extends RepositoriesAbstract implements PropertyInterface
{
    public function getRelatedProperties(int $propertyId, int $limit = 4, array $with = [], array $extra = []): Collection|LengthAwarePaginator
    {
        $limit = $limit > 1 ? $limit : 4;
        $currentProperty = $this->findById($propertyId, ['categories']);

        $this->model = $this->originalModel;

        // @phpstan-ignore-next-line
        $this->model = $this->model
            ->where('id', '<>', $propertyId)
            ->active();

        if ($currentProperty && $currentProperty->categories->count()) {
            $categoryIds = $currentProperty->categories->pluck('id')->toArray();

            $this->model
                ->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('category_id', $categoryIds);
                })
                ->where('type', $currentProperty->type);
        }

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
            'with' => $with,
        ], $extra);

        return $this->advancedGet($params);
    }

    public function getProperties(array $filters = [], array $params = []): Collection|LengthAwarePaginator
    {
        $filters = array_merge([
            'keyword' => null,
            'type' => null,
            'bedroom' => null,
            'bathroom' => null,
            'floor' => null,
            'min_square' => null,
            'max_square' => null,
            'min_price' => null,
            'max_price' => null,
            'project' => null,
            'project_id' => null,
            'category_id' => null,
            'author_id' => null,
            'city_id' => null,
            'city' => null,
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
                'price' => 'ASC',
            ],
            'price_desc' => [
                'price' => 'DESC',
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

        // Initialize the model with active properties
        $this->model = $this->originalModel->active();

        // Always sort by featured status first, then by featured_priority, then by the regular ordering
        $this->model = $this->model
            // First sort by featured status (featured properties first)
            ->orderByDesc('is_featured')
            // Then sort featured properties by their priority (higher values first)
            ->orderByDesc('featured_priority');

        // Then apply the regular ordering for properties with same featured status and priority
        foreach ($orderBy as $column => $direction) {
            $this->model = $this->model->orderBy($column, $direction);
        }

        // @phpstan-ignore-next-line

        if ($filters['keyword'] !== null) {
            $keyword = $filters['keyword'];

            if (is_plugin_active('language') && is_plugin_active('language-advanced') && Language::getCurrentLocale() != Language::getDefaultLocale()) {
                $this->model = $this->model
                    ->whereHas('translations', function (BaseQueryBuilder $query) use ($keyword): void {
                        $query
                            ->addSearch('name', $keyword, false, false)
                            ->addSearch('location', $keyword, false)
                            ->addSearch('description', $keyword, false)
                            ->addSearch('unique_id', $keyword, false);
                    });
            } else {
                $this->model = $this->model
                    ->where(function (BaseQueryBuilder $query) use ($keyword) {
                        return $query
                            ->addSearch('name', $keyword, false, false)
                            ->addSearch('location', $keyword, false)
                            ->addSearch('description', $keyword, false)
                            ->addSearch('unique_id', $keyword, false);
                    });
            }
        }

        if ($filters['type'] !== null) {
            if ($filters['type'] == PropertyTypeEnum::SALE) {
                $this->model = $this->model->where('type', $filters['type']);
            } else {
                $this->model = $this->model->where('type', $filters['type']);
            }
        }

        if (isset($filters['bedroom'])) {
            $bedroomValue = $filters['bedroom'];
            
            // Add detailed logging for debugging
            \Illuminate\Support\Facades\Log::info('Bedroom filter details', [
                'raw_value' => $bedroomValue,
                'value_type' => gettype($bedroomValue),
                'is_studio' => strtolower($bedroomValue) === 'studio'
            ]);
            
            if (strtolower($bedroomValue) === 'studio') {
                // For studio selection, we'll look for properties with 1 bedroom
                $this->model = $this->model->where('number_bedroom', 1);
                
                // Log the query for debugging
                \Illuminate\Support\Facades\Log::info('Studio filter applied', [
                    'sql' => $this->model->toSql(),
                    'bindings' => $this->model->getBindings(),
                    'filter_type' => 'studio (mapped to 1 bedroom)'
                ]);
            } else if (is_numeric($bedroomValue)) {
                $numBedrooms = (int)$bedroomValue;
                if ($numBedrooms < 5) {
                    // For 2-4 bedrooms, match exact number
                    $this->model = $this->model->where('number_bedroom', $numBedrooms);
                    
                    \Illuminate\Support\Facades\Log::info('Regular bedroom filter applied', [
                        'number_bedrooms' => $numBedrooms,
                        'filter_type' => 'exact_match'
                    ]);
                } else {
                    // For 5+ bedrooms
                    $this->model = $this->model->where('number_bedroom', '>=', 5);
                    
                    \Illuminate\Support\Facades\Log::info('5+ bedrooms filter applied', [
                        'filter_type' => 'five_plus'
                    ]);
                }
            }
        }

        if ($filters['bathroom']) {
            if ($filters['bathroom'] < 5) {
                $this->model = $this->model->where('number_bathroom', $filters['bathroom']);
            } else {
                $this->model = $this->model->where('number_bathroom', '>=', $filters['bathroom']);
            }
        }

        if ($filters['floor']) {
            if ($filters['floor'] < 5) {
                $this->model = $this->model->where('number_floor', $filters['floor']);
            } else {
                $this->model = $this->model->where('number_floor', '>=', $filters['floor']);
            }
        }

        if ($filters['min_square'] !== null || $filters['max_square'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minSquare = Arr::get($filters, 'min_square');
                    $maxSquare = Arr::get($filters, 'max_square');

                    if ($minSquare !== null) {
                        $query = $query->where('square', '>=', $minSquare);
                    }

                    if ($maxSquare !== null) {
                        $query = $query->where('square', '<=', $maxSquare);
                    }

                    return $query;
                });
        }

        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minPrice = Arr::get($filters, 'min_price');
                    $maxPrice = Arr::get($filters, 'max_price');

                    if ($minPrice !== null) {
                        $query = $query->where('price', '>=', $minPrice);
                    }

                    if ($maxPrice !== null) {
                        $query = $query->where('price', '<=', $maxPrice);
                    }

                    return $query;
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

        if ($filters['project'] !== null) {
            $this->model = $this->model->where(function (BaseQueryBuilder $query) use ($filters): void {
                $query
                    ->where('project_id', $filters['project'])
                    ->orWhereHas('project', function (BaseQueryBuilder $query) use ($filters): void {
                        $query->addSearch('re_projects.name', $filters['project'], false, false);
                    });
            });
        }

        if ($filters['project_id'] !== null) {
            $this->model = $this->model->where('project_id', $filters['project_id']);
        }

        if ($filters['author_id'] !== null) {
            $this->model = $this->model
                ->where('author_id', $filters['author_id'])
                ->where('author_type', Account::class);
        }

        if ($filters['category_id']) {
            $categoryId = $filters['category_id'];
            
            // Get the selected category
            $category = app(\Botble\RealEstate\Models\Category::class)->find($categoryId);
            if ($category) {
                $categoryIds = [$categoryId];
                
                // If it's a parent category (like "Villa"), include all its variants
                if ($category->is_parent) {
                    // Get all categories with same base name (e.g., "Villa", "Villa (Residential)", "Villa (Commercial)")
                    $similarCategories = app(\Botble\RealEstate\Models\Category::class)
                        ->where('name', 'LIKE', $category->base_name . '%')
                        ->pluck('id')
                        ->toArray();
                    $categoryIds = array_merge($categoryIds, $similarCategories);
                }
                // If it's a specific category (like "Villa (Residential)"), include its base category
                elseif ($category->parent_id) {
                    $categoryIds[] = $category->parent_id;
                }
                
                $this->model = $this->model
                    ->whereHas('categories', function ($query) use ($categoryIds) {
                        $query->whereIn('re_property_categories.category_id', $categoryIds);
                    });
                
                // Add logging for debugging
                \Illuminate\Support\Facades\Log::info('Category filtering', [
                    'selected_category' => $categoryId,
                    'category_ids' => $categoryIds,
                    'category_name' => $category->name,
                    'is_parent' => $category->is_parent,
                    'base_name' => $category->base_name
                ]);
            }
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
                $propertyIds = $this
                    ->getModel()
                    ->toBase()
                    ->select('re_properties.id')
                    ->join('re_property_features', 're_properties.id', '=', 're_property_features.property_id')
                    ->whereIn('re_property_features.feature_id', $features)
                    ->groupBy('re_properties.id')
                    ->havingRaw('COUNT(DISTINCT re_property_features.feature_id) = ' . count($features))
                    ->pluck('re_properties.id')
                    ->all();

                $this->model = $this->model->whereIn('id', $propertyIds);
            }
        }

        $this->model = apply_filters('properties_filter_query', $this->model, $filters, $params);

        return $this->advancedGet($params);
    }

    public function getProperty(int $propertyId, array $with = [], array $extra = []): ?Property
    {
        $params = array_merge([
            'condition' => [
                'id' => $propertyId,
                'moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'with' => $with,
            'take' => 1,
        ], $extra);

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel->notExpired();

        return $this->advancedGet($params);
    }

    public function getPropertiesByConditions(array $condition, int $limit = 4, array $with = []): Collection|LengthAwarePaginator
    {
        $limit = $limit > 1 ? $limit : 4;

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel->active();

        $params = [
            'condition' => $condition,
            'with' => $with,
            'take' => $limit,
            'order_by' => ['created_at' => 'DESC'],
        ];

        return $this->advancedGet($params);
    }
}
