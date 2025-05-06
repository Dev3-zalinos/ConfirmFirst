@php
    $itemsPerRow ??= 2;
@endphp

<div class="row row-cols-1 row-cols-lg-{{ $itemsPerRow }}">
    @foreach($properties as $property)
        <div class="col">
            @include(Theme::getThemeNamespace('views.real-estate.properties.item-list'))
        </div>
    @endforeach
</div>
