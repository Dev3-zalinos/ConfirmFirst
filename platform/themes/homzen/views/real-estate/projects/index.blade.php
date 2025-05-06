@php
    $itemLayout ??= request()->input('layout', 'grid');
    $itemLayout = in_array($itemLayout, ['grid', 'list']) ? $itemLayout : 'grid';

    $layout ??= theme_option('real_estate_project_listing_layout', 'top-map');

    if (! isset($itemsPerRow)) {
        $itemsPerRow = $itemLayout === 'grid' ? 3 : 2;
        if (! in_array($layout, ['top-map', 'without-map'])) {
            $itemsPerRow = $itemLayout === 'grid' ? 2 : 1;
        }
    }
@endphp

@if ($projects->isNotEmpty())
    @include(Theme::getThemeNamespace("views.real-estate.projects.$itemLayout"), compact('itemsPerRow'))
@else
    <div class="alert alert-warning" role="alert">
        {{ __('No projects found.') }}
    </div>
@endif

@if ($projects instanceof \Illuminate\Pagination\LengthAwarePaginator && $projects->hasPages())
    <div class="justify-content-center wd-navigation">
        {{ $projects->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
    </div>
@endif
