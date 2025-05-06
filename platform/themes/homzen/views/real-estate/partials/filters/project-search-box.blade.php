@php
    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');

    $style ??= 1;
@endphp

@if($style === 'sidebar')
    <div class="flat-tab flat-tab-form widget-filter-search widget-box bg-surface">
        <div class="h7 title fw-7">{{ __('Search') }}</div>
        <div class="form-sl">
            <div class="wd-filter-select">
                <div class="inner-group inner-filter">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.keyword'))
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.location'), ['style' => 3])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.categories'))
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.floor'), ['class' => 'form-style'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.block'), ['class' => 'form-style'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['class' => 'form-style', 'maxPrice' => get_max_projects_price()])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.flat'), ['class' => 'form-style wd-price-2'])

                    <div class="form-style mt-5">
                        <button type="submit" class="tf-btn primary">{{ __('Find Projects') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="flat-tab flat-tab-form">
        <div class="form-sl">
            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.base'))
            <div class="wd-search-form">
                <div class="grid-2 group-box group-price">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['maxPrice' => get_max_projects_price()])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.flat'))
                </div>
                <div class="group-box">
                    <div class="group-select grid-3">
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.floor'))
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.block'))
                    </div>
                </div>
                <button type="submit" class="tf-btn primary form-search-box-offcanvas-button mt-5">{{ __('Find Projects') }}</button>
            </div>
        </div>
    </div>
@endif
