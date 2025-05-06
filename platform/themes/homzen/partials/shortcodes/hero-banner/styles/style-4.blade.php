@php
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->usePath()->add('leaflet-markercluster', 'plugins/leaflet/MarkerCluster.css');
    Theme::asset()->usePath()->add('leaflet-markercluster-default', 'plugins/leaflet/MarkerCluster.Default.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');
    Theme::asset()->container('footer')->usePath()->add('leaflet-markercluster', 'plugins/leaflet/leaflet.markercluster.js');
@endphp

<section class="flat-map hero-banner-4">
    <div
        data-bb-toggle="list-map"
        id="map"
        style="min-height: 460px;"
        data-url="{{ route('public.ajax.properties.map') }}"
        data-tile-layer="{{ RealEstateHelper::getMapTileLayer() }}"
        data-center="{{ json_encode(RealEstateHelper::getMapCenterLatLng()) }}"
    ></div>

    @if(is_plugin_active('real-estate') && $shortcode->search_box_enabled)
        <div class="container">
            <div class="wrap-filter-search">
                @include(Theme::getThemeNamespace('views.real-estate.partials.search-box'), ['style' => 4, 'centeredTabs' => true])
            </div>
        </div>
    @endif
</section>

@include(Theme::getThemeNamespace('views.real-estate.partials.property-map-content'))
