@php
    Theme::layout('full-width');
    Theme::set('pageTitle', __('Projects'));
@endphp

@include(Theme::getThemeNamespace('views.real-estate.partials.listing'), [
    'actionUrl' => RealEstateHelper::getProjectsListPageUrl(),
    'ajaxUrl' => route('public.projects'),
    'mapUrl' => route('public.ajax.projects.map'),
    'perPages' => RealEstateHelper::getProjectsPerPageList(),
    'itemLayout' => request()->query('layout', 'grid'),
    'layout' => theme_option('real_estate_project_listing_layout', 'top-map'),
    'filterViewPath' => Theme::getThemeNamespace('views.real-estate.partials.filters.project-search-box'),
    'itemsViewPath' => Theme::getThemeNamespace('views.real-estate.projects.index'),
])

@include(Theme::getThemeNamespace('views.real-estate.partials.project-map-content'))
