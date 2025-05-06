<?php

namespace Theme\Homzen\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Location\Models\City;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use Theme\Homzen\Actions\GetPropertiesAction;
use Theme\Homzen\Http\Resources\ProjectResource;
use Theme\Homzen\Http\Resources\PropertyResource;

class HomzenController extends PublicController
{
    public function ajaxGetProperties(Request $request, GetPropertiesAction $getPropertiesAction): BaseHttpResponse
    {
        $request->validate([
            'type' => ['nullable', Rule::in(PropertyTypeEnum::values())],
            'limit' => ['required', 'integer'],
            'is_featured' => ['boolean'],
            'category_id' => ['nullable', 'string'],
            'category_ids' => ['nullable', 'array'],
        ]);

        $properties = $getPropertiesAction->handle(
            $request->integer('limit', 6),
            $request->input('category_id'),
            $request->string('type'),
            $request->boolean('is_featured'),
            (array) $request->input('category_ids', [])
        );

        return $this
            ->httpResponse()
            ->setData(view(
                Theme::getThemeNamespace('views.real-estate.properties.index'),
                ['properties' => $properties, 'itemsPerRow' => 3]
            )->render());
    }

    public function ajaxGetPropertiesForMap(Request $request): JsonResource
    {
        $validated = $request->validate([
            'k' => ['nullable', 'string'],
            'type' => ['nullable', Rule::in(PropertyTypeEnum::values())],
            'bedroom' => ['nullable', 'integer'],
            'bathroom' => ['nullable', 'integer'],
            'floor' => ['nullable', 'integer'],
            'min_price' => ['nullable', 'numeric'],
            'max_price' => ['nullable', 'numeric'],
            'min_square' => ['nullable', 'numeric'],
            'max_square' => ['nullable', 'numeric'],
            'project' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'city_id' => ['nullable', 'integer'],
            'location' => ['nullable', 'string'],
        ]);

        $params = [
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => $request->integer('page', 1),
            ],
        ];

        $properties = app(PropertyInterface::class)->getProperties($validated, $params);

        return $this
            ->httpResponse()
            ->setData(PropertyResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxSearchProjects(Request $request): BaseHttpResponse
    {
        $request->validate([
            'k' => ['nullable', 'string'],
        ]);

        $projects = Project::query()
            ->when($request->filled('k'), function (Builder $query) use ($request): void {
                $query->where('name', 'LIKE', '%' . $request->input('k') . '%');
            })
            ->select(['id', 'name'])
            ->take(10)
            ->oldest('name')
            ->get();

        return $this
            ->httpResponse()
            ->setData(view(
                Theme::getThemeNamespace('views.real-estate.partials.filters.projects-suggestion'),
                compact('projects')
            )->render());
    }

    public function ajaxGetProjectsForMap(Request $request, ProjectInterface $projectRepository)
    {
        $params = [
            'with' => RealEstateHelper::getProjectRelationsQuery(),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => $request->integer('page', 1),
            ],
        ];

        $projects = $projectRepository->getProjects($request->input(), $params);

        return $this
            ->httpResponse()
            ->setData(ProjectResource::collection($projects))
            ->toApiResponse();
    }

    public function ajaxGetCities(Request $request)
    {
        if (! is_plugin_active('location')) {
            return $this->httpResponse()->setData([]);
        }

        $request->validate([
            'location' => ['nullable', 'string'],
            'page' => ['nullable', 'integer'],
            'minimal' => ['nullable', 'boolean'],
        ]);

        // Handle minimal mode for niceSelect dropdown
        if ($request->boolean('minimal')) {
            $page = $request->integer('page', 1);
            $perPage = 10;

            // Use direct query to the City model for proper pagination
            $query = City::query()
                ->wherePublished()
                ->with('state')
                ->orderBy('name');

            // Add search filter if provided
            if ($request->input('location')) {
                $query->where('name', 'LIKE', '%' . $request->input('location') . '%');
            }

            // Get paginated results directly from database
            $cities = $query->paginate($perPage, ['*'], 'page', $page);

            // Format data for niceSelect
            $items = $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'text' => $city->name . ($city->state ? ', ' . $city->state->name : ''),
                ];
            });

            return $this->httpResponse()
                ->setData([
                    'items' => $items,
                    'has_more' => $cities->hasMorePages(),
                    'total' => $cities->total(),
                ])
                ->toApiResponse();
        }

        // For non-minimal requests, use the original implementation
        $cities = app(CityInterface::class)->filters($request->input('location'));

        return $this
            ->httpResponse()
            ->setData(
                view(
                    Theme::getThemeNamespace('views.real-estate.partials.filters.cities-suggestion'),
                    compact('cities')
                )->render()
            );
    }

    public function getWishlist(Request $request, PropertyInterface $propertyRepository, ProjectInterface $projectRepository)
    {
        abort_unless(RealEstateHelper::isEnabledWishlist(), 404);

        SeoHelper::setTitle(__('Wishlist'))
            ->setDescription(__('Wishlist'));

        $propertyWishlist = isset($_COOKIE['wishlist']) ? explode(',', $_COOKIE['wishlist']) : [];
        $propertyWishlist = array_filter($propertyWishlist);
        $projectWishlist = isset($_COOKIE['project_wishlist']) ? explode(',', $_COOKIE['project_wishlist']) : [];
        $projectWishlist = array_filter($projectWishlist);

        $properties = collect();
        $projects = collect();

        if (! empty($propertyWishlist)) {
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    ['re_properties.id', 'IN', $propertyWishlist],
                ],
                'order_by' => [
                    're_properties.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'current_paged' => $request->integer('page', 1),
                ],
                'with' => RealEstateHelper::getPropertyRelationsQuery(),
            ]);
        }

        if (! empty($projectWishlist)) {
            $projects = $projectRepository->advancedGet([
                'condition' => [
                    ['re_projects.id', 'IN', $projectWishlist],
                ],
                'order_by' => [
                    're_projects.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'current_paged' => $request->integer('page', 1),
                ],
                'with' => RealEstateHelper::getProjectRelationsQuery(),
            ]);
        }

        Theme::breadcrumb()->add(__('Wishlist'));

        return Theme::scope('real-estate.wishlist', compact('properties', 'projects'))->render();
    }
}
