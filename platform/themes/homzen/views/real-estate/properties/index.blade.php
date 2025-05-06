@php
    $itemLayout ??= request()->input('layout', 'grid');
    $itemLayout = in_array($itemLayout, ['grid', 'list']) ? $itemLayout : 'grid';
    $layout ??= get_property_listing_page_layout();

    if (! isset($itemsPerRow)) {
        $itemsPerRow = $itemLayout === 'grid' ? 3 : 2;
        if (! in_array($layout, ['top-map', 'without-map'])) {
            $itemsPerRow = $itemLayout === 'grid' ? 2 : 1;
        }
    }
@endphp

@if ($properties->isNotEmpty())
    @include(Theme::getThemeNamespace("views.real-estate.properties.$itemLayout"), compact('itemsPerRow'))
@else
    <div class="alert alert-warning" role="alert">
        {{ __('No properties found.') }}
    </div>
@endif

@if ($properties instanceof \Illuminate\Pagination\LengthAwarePaginator && $properties->hasPages())
    <div class="justify-content-center wd-navigation mt-5">
        {{ $properties->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
    </div>
@endif
