<?php

use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;
use Theme\Homzen\Http\Controllers\HomzenController;


Route::middleware(['web', 'core'])
    ->controller(HomzenController::class)
    ->group(function (): void {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function (): void {
            Route::get('wishlist', 'getWishlist')->name('public.wishlist');

            Route::prefix('ajax')->name('public.ajax.')->middleware(RequiresJsonRequestMiddleware::class)->group(function (): void {
                Route::get('properties', 'ajaxGetProperties')->name('properties');
                Route::get('properties/map', 'ajaxGetPropertiesForMap')->name('properties.map');
                Route::get('projects/map', 'ajaxGetProjectsForMap')->name('projects.map');
                Route::get('projects/search', 'ajaxSearchProjects')->name('projects.search');
                Route::get('cities', 'ajaxGetCities')->name('cities');
            });
        });
    });

Theme::routes();
