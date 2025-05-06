@php
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');
    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
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

    <section class="flat-section-v6 flat-recommended flat-sidebar">
        <div class="container">
            @include(Theme::getThemeNamespace('views.real-estate.partials.listing-top'))

            {!! apply_filters('ads_render', null, 'listing_page_before') !!}

            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <div class="search-box-offcanvas">
                        <div class="search-box-offcanvas-backdrop"></div>
                        <div class="search-box-offcanvas-content">
                            <div class="search-box-offcanvas-header">
                                <h3>{{ __('Filter') }}</h3>

                                <button type="button" class="btn-close" data-bb-toggle="toggle-filter-offcanvas"></button>
                            </div>
                            <div class="widget-sidebar">
                                @include($filterViewPath, ['style' => 'sidebar'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="position-relative" data-bb-toggle="data-listing">
                        @include($itemsViewPath, ['itemLayout' => $itemLayout, 'itemsPerRow' => $itemLayout === 'grid' ? 2 : 1])
                    </div>
                </div>
            </div>

            {!! apply_filters('ads_render', null, 'listing_page_after') !!}
        </div>
    </section>
</form>
