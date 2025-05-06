@php
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->usePath()->add('leaflet-markercluster', 'plugins/leaflet/MarkerCluster.css');
    Theme::asset()->usePath()->add('leaflet-markercluster-default', 'plugins/leaflet/MarkerCluster.Default.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');
    Theme::asset()->container('footer')->usePath()->add('leaflet-markercluster', 'plugins/leaflet/leaflet.markercluster.js');
@endphp

<div
    data-bb-toggle="list-map"
    id="map"
    style="min-height: 400px;"
    data-url="{{ $mapUrl }}"
    data-tile-layer="{{ RealEstateHelper::getMapTileLayer() }}"
    data-center="{{ json_encode(RealEstateHelper::getMapCenterLatLng()) }}"
></div>
