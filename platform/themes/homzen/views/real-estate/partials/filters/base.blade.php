<div @class(['wd-find-select' =>  in_array($style, [1, 2, 4]), 'wd-filter-select' => $style === 3, 'style-2 shadow-st' => $style === 2, 'no-left-round' => $noLeftRound ?? false])>
    <div class="inner-group">
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.keyword'))
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.location'))
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.categories'))

        @if (theme_option('real_estate_enable_advanced_search', 'yes') == 'yes')
            <div @class(['form-group-4 box-filter', 'form-style' => $style === 3])>
                <a class="filter-advanced pull-right" href="#">
                    <x-core::icon name="ti ti-adjustments-alt" />
                    <span @class(['text-1' => $style !== 3, 'text-advanced fw-7' => $style === 3])>{{ __('Advanced') }}</span>
                </a>
            </div>
        @endif
    </div>
    @if($style === 3)
        <div class="form-style">
    @endif
    <button type="submit" class="tf-btn primary">{{ __('Search') }}</button>
    @if($style === 3)
        </div>
    @endif
</div>
