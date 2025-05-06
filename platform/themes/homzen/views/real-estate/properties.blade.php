@php
    Theme::layout('full-width');
    Theme::set('pageTitle', __('Properties'));
@endphp

@include(Theme::getThemeNamespace('views.real-estate.partials.listing'), [
    'actionUrl' => RealEstateHelper::getPropertiesListPageUrl(),
    'ajaxUrl' => route('public.properties'),
    'mapUrl' => route('public.ajax.properties.map'),
    'itemLayout' => request()->input('layout', 'grid'),
    'layout' => theme_option('real_estate_property_listing_layout', 'top-map'),
    'perPages' => RealEstateHelper::getPropertiesPerPageList(),
    'filterViewPath' => Theme::getThemeNamespace('views.real-estate.partials.filters.property-search-box'),
    'itemsViewPath' => Theme::getThemeNamespace('views.real-estate.properties.index'),
])

@include(Theme::getThemeNamespace('views.real-estate.partials.property-map-content'))
