@php
    Theme::layout('full-width');
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.css');
    Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');

    $style = theme_option('real_estate_property_detail_layout', 1);
    $style = in_array($style, range(1, 4)) ? $style : 1;
    Theme::set('pageTitle', $property->name);
@endphp

@include(Theme::getThemeNamespace("views.real-estate.single-layouts.style-$style"))

<template id="map-popup-content">
    <div class="map-listing-item">
        <div class="inner-box">
            <div class="image-box">
                <a href="{{ $property->url }}">
                    {{ RvMedia::image($property->image_thumb, $property->name) }}
                </a>
                {!! BaseHelper::clean($property->status_html) !!}
            </div>
            <div class="content">
                @if($property->short_address)
                    <p class="location">
                        <x-core::icon name="ti ti-map-pin" />
                        {{ $property->short_address }}
                    </p>
                @endif
                <div class="title">
                    <a href="{{ $property->url }}" title="{{ $property->name }}">
                        {{ $property->name }}
                    </a>
                </div>
                <div class="price">{{ $property->price_html }}</div>
                <ul class="list-info">
                    <li>
                        <x-core::icon name="ti ti-bed" />
                        {{ number_format($property->number_bedroom) }}
                    </li>
                    <li>
                        <x-core::icon name="ti ti-bath" />
                        {{ number_format($property->number_bathroom) }}
                    </li>
                    <li>
                        <x-core::icon name="ti ti-ruler" />
                        {{ $property->square_text }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
