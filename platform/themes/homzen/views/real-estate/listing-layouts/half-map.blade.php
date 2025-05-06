@php
    Theme::set('breadcrumbEnabled', 'no');
    Theme::set('headerClass', 'no-line');
@endphp

<form
    action="{{ $actionUrl }}"
    data-url="{{ $ajaxUrl }}"
    method="get"
    class="filter-form"
>
    @csrf

    <input type="hidden" name="page" value="{{ BaseHelper::stringify(request()->integer('page')) }}" />
    <input type="hidden" name="layout" value="{{ BaseHelper::stringify(request()->input('layout')) }}" />

    <section class="flat-filter-search-v2">
        <div class="search-box-offcanvas">
            <div class="search-box-offcanvas-backdrop"></div>
            <div class="search-box-offcanvas-content">
                <div class="search-box-offcanvas-header">
                    <h3>{{ __('Filter') }}</h3>

                    <button type="button" class="btn-close" data-bb-toggle="toggle-filter-offcanvas"></button>
                </div>
                @include($filterViewPath)
            </div>
        </div>
    </section>

    <section class="wrapper-layout layout-2">
        <div class="wrap-left">
            @include(Theme::getThemeNamespace('views.real-estate.partials.listing-top'), ['class' => 'style-1'])

            {!! apply_filters('ads_render', null, 'listing_page_before') !!}

            <div class="position-relative mb-3" data-bb-toggle="data-listing">
                @include($itemsViewPath, ['itemLayout' => $itemLayout, 'itemsPerRow' => $itemLayout === 'grid' ? 2 : 1])
            </div>

            {!! apply_filters('ads_render', null, 'listing_page_after') !!}
        </div>


        <div class="wrap-right">
            @include(Theme::getThemeNamespace('views.real-estate.partials.map'))
        </div>
    </section>
</form>
