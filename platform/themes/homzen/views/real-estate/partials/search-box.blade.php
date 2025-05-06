@php
    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');

    $style ??= 1;
    $noLeftRound ??= false;
    $centeredTabs ??= false;

    $projectsSearchEnabled = (bool) ($shortcode->projects_search_enabled ?? true);

    $selectedTabs = explode(',', $shortcode->tabs ?: 'sale,project');

    $defaultSearchType = $shortcode->default_search_type ?: 'sale';
    $tabs = collect(['project' => __('Project'), 'rent' => __('For Rent'), 'sale' => __('For Sale')])
        ->when(! RealEstateHelper::isEnabledProjects() || ! $projectsSearchEnabled, function ($tabs) use (&$projectsSearchEnabled) {
            $projectsSearchEnabled = false;

            return $tabs->forget('project');
        })
        ->reject(fn ($tab, $key) => $key !== 'project' && ! in_array($key, RealEstateHelper::enabledPropertyTypes()))
        ->reject(fn ($tab, $key) => ! in_array($key, $selectedTabs))
        ->sortBy(fn ($tab, $key) => array_search($key, $selectedTabs));

    if ($defaultSearchType === 'project' && ! in_array('project', $tabs->keys()->toArray())) {
        $defaultSearchType = $tabs->keys()->first();
    }

    $maxPrice = 500000000; // Set maximum price to 500 million
@endphp

@if ($tabs->isNotEmpty())
    <div class="flat-tab flat-tab-form">
        @if (count($tabs) > 1)
            <ul @class(['nav-tab-form', 'style-1' => $style === 1, 'style-2' => in_array($style, [2, 3]), 'style-3' => $style === 4, 'justify-content-center' => $centeredTabs]) role="tablist">
                @foreach ($tabs as $key => $tab)
                    <li class="nav-tab-item" role="presentation">
                        <a
                            href="javascript:void(0);"
                            @class(['nav-link-item', 'active' => $key === $defaultSearchType])
                            data-bs-toggle="tab"
                            data-bb-toggle="change-search-type"
                            data-value="{{ $key }}"
                            data-url="{{ $key === 'project' ? RealEstateHelper::getProjectsListPageUrl() : RealEstateHelper::getPropertiesListPageUrl() }}"
                        >
                            {{ $tab }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="tab-content">
            <div class="tab-pane fade active show" role="tabpanel">
                <form
                    @class(['form-sl', 'flat-filter-form' => $style === 3])
                    @if ($projectsSearchEnabled && $defaultSearchType === 'project')
                        action="{{ RealEstateHelper::getProjectsListPageUrl() }}"
                    @else
                        action="{{ RealEstateHelper::getPropertiesListPageUrl() }}"
                    @endif
                    method="get"
                    id="hero-search-form"
                >

                    <input
                        name="type"
                        type="hidden"
                        @if ($projectsSearchEnabled && $defaultSearchType === 'project')
                            value="project"
                        @else
                            value="{{ $defaultSearchType }}"
                        @endif
                    >
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.base'))

                    @if (theme_option('real_estate_enable_advanced_search', 'yes') == 'yes')
                        @if ($projectsSearchEnabled)
                            <div class="wd-search-form project-search-form">
                                <div class="grid-2 group-box group-price">
                                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price', ['maxPrice' => $maxPrice]))
                                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.flat'))
                                </div>
                                <div class="group-box">
                                    <div class="group-select grid-3">
                                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.floor'))
                                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.block'))
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="wd-search-form property-search-form" @style(['display: none;' => $projectsSearchEnabled && $defaultSearchType === 'project'])>
                            <div class="grid-2 group-box group-price">
                                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['maxPrice' => $maxPrice])
                                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.square'))
                            </div>
                            <div class="group-box">
                                <div class="group-select grid-3">
                                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'))
                                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bathroom'))
                                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.floor'))
                                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.projects'))
                                </div>
                            </div>
                            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.features'))
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endif