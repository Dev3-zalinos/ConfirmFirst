<section class="flat-section career-single">
    <div class="career-single-content">
        <h2 class="career-name">{{ $career->name }}</h2>
        <div class="career-meta">
            <div class="career-meta-item">
                <x-core::icon name="ti ti-clock" />
                {{ Theme::formatDate($career->created_at) }}
            </div>
            <div class="career-meta-item">
                <x-core::icon name="ti ti-map-pin" />
                {{ $career->location }}
            </div>
            <div class="career-meta-item">
                <x-core::icon name="ti ti-cash" />
                {{ $career->salary }}
            </div>
        </div>
        <div class="ck-content single-detail career-content">
            {!! BaseHelper::clean($career->content) !!}
        </div>
    </div>

    <div class="career-related">
        <h3 class="career-related-title">{{ __('Related Careers') }}</h3>
        @include(Theme::getThemeNamespace('views.career.partials.list'), ['careers' => $relatedCareers])
    </div>
</section>
