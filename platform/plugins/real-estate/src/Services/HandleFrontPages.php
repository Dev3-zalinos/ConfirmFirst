<?php

namespace Botble\RealEstate\Services;

use Botble\Base\Supports\Helper;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\QueryBuilders\ProjectBuilder;
use Botble\RealEstate\QueryBuilders\PropertyBuilder;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Models\Slug;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HandleFrontPages
{
    public function handle(Slug|array $slug)
    {
        if (! $slug instanceof Slug) {
            return $slug;
        }

        $request = request();

        $isPreviewing = $request->input('preview') && (Auth::guard()->check() || Auth::guard('account')->check());

        switch ($slug->reference_type) {
            case Property::class:

                $condition = [
                    'id' => $slug->reference_id,
                ];

                if (! $isPreviewing) {
                    $condition = [
                        ...$condition,
                        ...RealEstateHelper::getPropertyDisplayQueryConditions(),
                    ];
                }

                $reviewData = RealEstateHelper::getReviewExtraData();

                $property = Property::query()
                    ->where($condition)
                    ->notExpired()
                    ->with(RealEstateHelper::getPropertyRelationsQuery())
                    ->when($reviewData, function (PropertyBuilder $query) use ($reviewData) {
                        return $query
                            ->withCount($reviewData['withCount'])
                            ->withAvg($reviewData['withAvg'][0], $reviewData['withAvg'][1]);
                    })
                    ->firstOrFail();

                if ($property->slugable->key !== $slug->key) {
                    return redirect()->to($property->url);
                }

                SeoHelper::setTitle($property->name)
                    ->setDescription(Str::words($property->description, 120));

                $meta = new SeoOpenGraph();
                if ($property->image) {
                    $meta->setImage(RvMedia::getImageUrl($property->image));
                }
                $meta->setDescription($property->description);
                $meta->setUrl($property->url);
                $meta->setTitle($property->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                Theme::breadcrumb()
                    ->add(__('Properties'), route('public.properties'))
                    ->add($property->name);

                Helper::handleViewCount($property, 'viewed_property');

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PROPERTY_MODULE_SCREEN_NAME, $property);

                if (function_exists('admin_bar')) {
                    admin_bar()->registerLink(__('Edit this property'), route('property.edit', $property->id));
                }

                $images = [];
                if (! empty($property->images) && is_array($property->images)) {
                    foreach ($property->images as $image) {
                        $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
                    }
                }

                return [
                    'view' => 'real-estate.property',
                    'default_view' => 'plugins/real-estate::themes.property',
                    'data' => compact('property', 'images'),
                    'slug' => $property->slug,
                ];

            case Project::class:
                abort_unless(RealEstateHelper::isEnabledProjects(), 404);

                $condition = [
                    'id' => $slug->reference_id,
                ];

                if (! $isPreviewing) {
                    $condition = [
                        ...$condition,
                        ...RealEstateHelper::getProjectDisplayQueryConditions(),
                    ];
                }

                $reviewData = RealEstateHelper::getReviewExtraData();

                /**
                 * @var Project $project
                 */
                $project = Project::query()
                    ->where($condition)
                    ->with(RealEstateHelper::getProjectRelationsQuery())
                    ->when($reviewData, function (ProjectBuilder $query) use ($reviewData) {
                        return $query
                            ->withCount($reviewData['withCount'])
                            ->withAvg($reviewData['withAvg'][0], $reviewData['withAvg'][1]);
                    })
                    ->firstOrFail();

                if ($project->slugable->key !== $slug->key) {
                    return redirect()->to($project->url);
                }

                SeoHelper::setTitle($project->name)
                    ->setDescription(Str::words($project->description, 120));

                $meta = new SeoOpenGraph();
                if ($project->image) {
                    $meta->setImage(RvMedia::getImageUrl($project->image));
                }
                $meta->setDescription($project->description);
                $meta->setUrl($project->url);
                $meta->setTitle($project->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                Theme::breadcrumb()
                    ->add(__('Projects'), route('public.projects'))
                    ->add($project->name);

                $relatedProjects = app(ProjectInterface::class)->getRelatedProjects(
                    $project->getKey(),
                    (int) theme_option('number_of_related_projects', 8)
                );

                if (function_exists('admin_bar')) {
                    admin_bar()->registerLink(__('Edit this project'), route('project.edit', $project->id));
                }

                Helper::handleViewCount($project, 'viewed_project');

                do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PROJECT_MODULE_SCREEN_NAME, $project);

                $images = [];

                if (! empty($project->images) && is_array($project->images)) {
                    foreach ($project->images as $image) {
                        $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
                    }
                }

                return [
                    'view' => 'real-estate.project',
                    'default_view' => 'plugins/real-estate::themes.real-estate',
                    'data' => compact('project', 'images', 'relatedProjects'),
                    'slug' => $project->slug,
                ];

            case Category::class:
                $category = Category::query()
                    ->where('id', $slug->reference_id)
                    ->with(['slugable'])
                    ->firstOrFail();

                SeoHelper::setTitle($category->name)
                    ->setDescription(Str::words($category->description, 120));

                $meta = new SeoOpenGraph();
                $meta->setDescription($category->description);
                $meta->setUrl($category->url);
                $meta->setTitle($category->name);
                $meta->setType('article');

                SeoHelper::setSeoOpenGraph($meta);

                Theme::breadcrumb()->add($category->name);

                $filters = [
                    'category_id' => $category->getKey(),
                ];

                $perPage = (int) theme_option('number_of_properties_per_page', 12);

                $params = [
                    'paginate' => [
                        'per_page' => $perPage ?: 12,
                        'current_paged' => $request->integer('page', 1),
                    ],
                    'order_by' => ['re_properties.created_at' => 'DESC'],
                    'with' => RealEstateHelper::getPropertyRelationsQuery(),
                ];

                $properties = app(PropertyInterface::class)->getProperties($filters, $params);

                return [
                    'view' => 'real-estate.property-category',
                    'default_view' => 'plugins/real-estate::themes.property-category',
                    'data' => compact('category', 'properties'),
                    'slug' => $category->slug,
                ];

            case Account::class:
                abort_if(RealEstateHelper::isDisabledPublicProfile(), 404);

                $account = Account::query()
                    ->where([
                        'id' => $slug->reference_id,
                        'is_public_profile' => true,
                    ])
                    ->firstOrFail();

                SeoHelper::setTitle($account->name);

                Theme::breadcrumb()->add($account->name);

                if (function_exists('admin_bar')) {
                    admin_bar()->registerLink(__('Edit this agent'), route('account.edit', $account->getKey()));
                }

                $filters = [
                    'author_id' => $account->getKey(),
                ];

                $params = [
                    'paginate' => [
                        'per_page' => 12,
                        'current_paged' => $request->integer('page', 1),
                    ],
                    'order_by' => ['re_properties.created_at' => 'DESC'],
                    'with' => RealEstateHelper::getPropertyRelationsQuery(),
                ];

                $properties = app(PropertyInterface::class)->getProperties($filters, $params);

                return [
                    'view' => 'real-estate.agent',
                    'default_view' => 'plugins/real-estate::themes.agent',
                    'data' => compact('account', 'properties'),
                    'slug' => $account->slug,
                ];
        }

        return $slug;
    }
}
