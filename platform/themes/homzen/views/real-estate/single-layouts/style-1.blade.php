@include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.gallery-slider'), ['model' => $property])

<section class="flat-section pt-0 flat-property-detail">
    <div class="container">
        {!! apply_filters('ads_render', null, 'detail_page_before') !!}

        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.header'))

        <div class="row">
            <div class="col-lg-8">
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

                {!! apply_filters(
                    BASE_FILTER_PUBLIC_COMMENT_AREA,
                    null,
                    $property
                ) !!}

                @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.reviews'), ['model' => $property, 'class' => 'single-property-element'])
            </div>
            <div class="col-lg-4">
                <div class="widget-sidebar wrapper-sidebar-right">
                    {!! apply_filters('ads_render', null, 'detail_page_sidebar_before') !!}

                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.contact'), ['class' => 'bg-surface'])

                    {!! apply_filters('ads_render', null, 'detail_page_sidebar_after') !!}
                </div>
            </div>
        </div>

        {!! apply_filters('ads_render', null, 'detail_page_after') !!}
    </div>
</section>

@include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.related-properties'))
