@php
    $itemsPerRow ??= 2;
@endphp

<div class="row row-cols-1 row-cols-lg-{{ $itemsPerRow }}">
    @foreach($projects as $project)
        <div class="col">
            @include(Theme::getThemeNamespace('views.real-estate.projects.item-list'))
        </div>
    @endforeach
</div>
