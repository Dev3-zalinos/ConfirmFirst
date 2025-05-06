@php
    Theme::set('breadcrumbEnabled', 'no');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');

    $itemLayout = request()->input('layout', $itemLayout ?? 'grid');
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

    <section class="flat-map">
        @include(Theme::getThemeNamespace('views.real-estate.partials.map'))
        <div class="search-box-offcanvas container">
            <div class="search-box-offcanvas-backdrop"></div>
            <div class="search-box-offcanvas-content">
                <div class="search-box-offcanvas-header">
                    <h3>{{ __('Filter') }}</h3>

                    <button type="button" class="btn-close" data-bb-toggle="toggle-filter-offcanvas"></button>
                </div>
                <div class="wrap-filter-search">
                    @include($filterViewPath, ['style' => 2])
                </div>
            </div>
        </div>
    </section>

    <section class="flat-section-v5 flat-recommended flat-recommended-v2">
        <div class="container">
            @include(Theme::getThemeNamespace('views.real-estate.partials.listing-top'))

            {!! apply_filters('ads_render', null, 'listing_page_before') !!}

            <div class="position-relative" data-bb-toggle="data-listing">
                @include($itemsViewPath, compact('itemLayout'))
            </div>

            {!! apply_filters('ads_render', null, 'listing_page_after') !!}
        </div>
    </section>
</form>
