 <div @class(['single-property-map', $class ?? null])>
    <div class="h7 title fw-7">{{ __('Location') }}</div>

     @if (theme_option('real_estate_show_map_on_single_detail_page', 'yes') === 'yes')
        @if ($property->latitude && $property->longitude)
            <div data-bb-toggle="detail-map" id="map" style="min-height: 400px;" data-tile-layer="{{ RealEstateHelper::getMapTileLayer() }}" data-center="{{ json_encode([$property->latitude, $property->longitude]) }}" data-map-icon="{{ $property->map_icon }}"></div>
        @else
            <iframe width="100%" style="min-height: 400px" src="https://maps.google.com/maps?q={{ urlencode($property->location) }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
        @endif
    @endif

     @if ($locationOnMap = ($property->location ?: $property->short_address))
         @php
             $mapUrl = 'https://www.google.com/maps/search/' . urlencode($locationOnMap);

             if ($property->latitude && $property->longitude) {
                 $mapUrl = 'https://maps.google.com/?q=' . $property->latitude . ',' . $property->longitude;
             }
         @endphp
         <ul class="info-map">
             <li>
                 <div class="fw-7">{{ __('Address') }}</div>
                 <a class="mt-4 text-variant-1" href="{{ $mapUrl }}" target="_blank">
                     {{ $locationOnMap }}
                 </a>
             </li>
         </ul>
     @endif
</div>
