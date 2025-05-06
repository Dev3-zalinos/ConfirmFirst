<section
    class="flat-section-v2 flat-testimonial-v2 wow fadeInUpSmall"
    data-wow-delay=".2s"
    data-wow-duration="2000ms"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        @if($shortcode->title || $shortcode->subtitle || $shortcode->description)
            <div class="box-title text-center position-relative">
                @if($shortcode->subtitle)
                    <div class="text-subtitle text-white">
                        {!! BaseHelper::clean($shortcode->subtitle) !!}
                    </div>
                @endif
                @if($shortcode->title)
                    <h2 class="section-title mt-4 text-white">
                        {!! BaseHelper::clean($shortcode->title) !!}
                    </h2>
                @endif

                @if ($shortcode->description)
                    <p class="p-16 body-2 text-white mt-3">{!! BaseHelper::clean($shortcode->description) !!}</p>
                @endif
            </div>
        @endif

        <div
            class="swiper tf-sw-testimonial"
            data-preview-lg="3"
            data-preview-md="2"
            data-preview-sm="2"
            data-space="30"
            {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
        >
            <div class="swiper-wrapper">
                @foreach ($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <div class="box-tes-item style-1">
                            @include(Theme::getThemeNamespace('partials.shortcodes.testimonials.partials.content'))
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="sw-pagination sw-pagination-testimonial"></div>
        </div>
    </div>
</section>
