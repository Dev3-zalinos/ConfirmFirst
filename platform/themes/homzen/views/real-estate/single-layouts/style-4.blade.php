<section class="flat-section-v6 flat-property-detail-v4">
    <div class="container">
        {!! apply_filters('ads_render', null, 'detail_page_before') !!}

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.header'))

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.gallery-slider-thumbnail'))

        {!! apply_filters('before_single_content_detail', null, $property) !!}

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.description'), ['class' => 'single-property-element'])

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.video'), ['class' => 'single-property-element'])

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.features'), ['class' => 'single-property-element'])

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.facilities'), ['class' => 'single-property-element'])

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.project'), ['class' => 'single-property-element'])

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.map'), ['class' => 'single-property-element'])

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.floor-plans'), ['class' => 'single-property-element'])

        {!! apply_filters('after_single_content_detail', null, $property) !!}

        <div class="wrapper-onepage">
            @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), ['model' => $property])
        </div>

        <div class="single-property-element single-property-contact">
            @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.contact'), ['class' => 'bg-surface'])
        </div>

        {!! apply_filters(
            BASE_FILTER_PUBLIC_COMMENT_AREA,
            null,
            $property
        ) !!}

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.reviews'), ['model' => $property, 'class' => 'single-property-element'])

        {!! apply_filters('ads_render', null, 'detail_page_after') !!}
    </div>
</section>

@include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.related-properties'))
