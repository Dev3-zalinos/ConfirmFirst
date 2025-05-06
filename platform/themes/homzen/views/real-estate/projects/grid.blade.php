@php
    $itemsPerRow ??= 3;
@endphp

@if ($projects->isNotEmpty())
    <div class="row row-cols-1 row-cols-sm-2 @if ($itemsPerRow > 2) row-cols-md-{{ $itemsPerRow - 1 }} @endif row-cols-xl-{{ $itemsPerRow }}">
        @foreach($projects as $project)
            <div class="col">
                @include(Theme::getThemeNamespace('views.real-estate.projects.item-grid'))
            </div>
        @endforeach
    </div>
@endif
