@php
    $itemsPerRow ??= 3;
@endphp

@if ($properties->isNotEmpty())
    <div class="row row-cols-1 row-cols-sm-2 @if ($itemsPerRow > 2) row-cols-md-{{ $itemsPerRow - 1 }} @endif row-cols-xl-{{ $itemsPerRow }}">
        @foreach($properties as $property)
            <div class="col">
                @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'))
            </div>
        @endforeach
    </div>
@endif
