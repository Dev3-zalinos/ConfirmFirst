<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Models\City;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;
use Illuminate\Support\Arr;

class AddSitemapListener
{
    public function handle(RenderingSiteMapEvent $event): void
    {
        if ($key = $event->key) {
            switch ($key) {
                case 'agents':

                    if (RealEstateHelper::isDisabledPublicProfile()) {
                        break;
                    }

                    $agentLastUpdated = Account::query()
                        ->latest('updated_at')
                        ->value('updated_at');

                    SiteMapManager::add(route('public.agents'), $agentLastUpdated, '0.4', 'monthly');

                    $items = Account::query()
                        ->latest('created_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add($item->url, $item->updated_at, '0.8');
                    }

                    break;

                case 'property-categories':

                    $items = Category::query()
                        ->with('slugable')
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->latest('created_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add($item->url, $item->updated_at, '0.8');
                    }

                    break;

                case 'properties-city':

                    $items = City::query()
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->latest('updated_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add(route('public.properties-by-city', $item->slug), $item->updated_at, '0.8');
                    }

                    break;

                case 'projects-city':
                    if (! RealEstateHelper::isEnabledProjects()) {
                        break;
                    }

                    $items = City::query()
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->latest('updated_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add(route('public.projects-by-city', $item->slug), $item->updated_at, '0.8');
                    }

                    break;
            }

            if (preg_match('/^properties-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])$/', $key, $matches)) {
                if (($year = Arr::get($matches, 1)) && ($month = Arr::get($matches, 2))) {
                    $properties = Property::query()
                        ->active()
                        ->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', $month)
                        ->latest('updated_at')
                        ->select(['id', 'name', 'updated_at'])
                        ->with(['slugable'])
                        ->get();

                    foreach ($properties as $property) {
                        if (! $property->slugable) {
                            continue;
                        }

                        SiteMapManager::add($property->url, $property->updated_at, '0.8');
                    }
                }
            }

            if (RealEstateHelper::isEnabledProjects()) {
                if (preg_match('/^projects-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])$/', $key, $matches)) {
                    if (($year = Arr::get($matches, 1)) && ($month = Arr::get($matches, 2))) {
                        $projects = Project::query()
                            ->active()
                            ->whereYear('updated_at', $year)
                            ->whereMonth('updated_at', $month)
                            ->latest('updated_at')
                            ->select(['id', 'name', 'updated_at'])
                            ->with(['slugable'])
                            ->get();

                        foreach ($projects as $project) {
                            if (! $project->slugable) {
                                continue;
                            }

                            SiteMapManager::add($project->url, $project->updated_at, '0.8');
                        }
                    }
                }
            }

            return;
        }

        $properties = Property::query()
            ->selectRaw('YEAR(updated_at) as updated_year, MONTH(updated_at) as updated_month, MAX(updated_at) as updated_at')
            ->active()
            ->groupBy('updated_year', 'updated_month')
            ->orderBy('updated_year', 'desc')
            ->orderBy('updated_month', 'desc')
            ->get();

        foreach ($properties as $property) {
            $key = sprintf('properties-%s-%s', $property->updated_year, str_pad($property->updated_month, 2, '0', STR_PAD_LEFT));
            SiteMapManager::addSitemap(SiteMapManager::route($key), $property->updated_at);
        }

        if (RealEstateHelper::isEnabledProjects()) {
            $projects = Project::query()
                ->selectRaw('YEAR(updated_at) as updated_year, MONTH(updated_at) as updated_month, MAX(updated_at) as updated_at')
                ->active()
                ->groupBy('updated_year', 'updated_month')
                ->orderBy('updated_year', 'desc')
                ->orderBy('updated_month', 'desc')
                ->get();

            foreach ($projects as $project) {
                $key = sprintf('projects-%s-%s', $project->updated_year, str_pad($project->updated_month, 2, '0', STR_PAD_LEFT));
                SiteMapManager::addSitemap(SiteMapManager::route($key), $project->updated_at);
            }
        }

        if (! RealEstateHelper::isDisabledPublicProfile()) {
            $agentLastUpdated = Account::query()
                ->latest('updated_at')
                ->value('updated_at');

            SiteMapManager::addSitemap(SiteMapManager::route('agents'), $agentLastUpdated);
        }

        $cityLastUpdated = City::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('properties-city'), $cityLastUpdated);

        if (RealEstateHelper::isEnabledProjects()) {
            SiteMapManager::addSitemap(SiteMapManager::route('projects-city'), $cityLastUpdated);
        }
    }
}
