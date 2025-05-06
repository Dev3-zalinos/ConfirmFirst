<section
    class="flat-section flat-testimonial-v4"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode]) !!}

        @if ($shortcode->description)
            <p class="text-center body-2 mb-5">{!! BaseHelper::clean($shortcode->description) !!}</p>
        @endif

        <div
            class="swiper tf-sw-testimonial"
            data-preview-lg="2"
            data-preview-md="2"
            data-preview-sm="2"
            data-space="30"
            {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
        >
            <div class="swiper-wrapper">
                @foreach ($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <div class="box-tes-item style-2">
                            @include(Theme::getThemeNamespace('partials.shortcodes.testimonials.partials.content'))
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="sw-pagination sw-pagination-testimonial"></div>
        </div>
    </div>
</section>
