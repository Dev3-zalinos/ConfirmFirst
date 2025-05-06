<section
    class="flat-section-v3 flat-testimonial"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="cus-layout-1">
        <div class="row align-items-center">
            <div class="col-lg-3">
                {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false, 'animation' => false]) !!}

                @if ($shortcode->description)
                    <p class="text-variant-1 p-16">{!! BaseHelper::clean($shortcode->description) !!}</p>
                @endif

                <div class="box-navigation">
                    <div class="navigation swiper-nav-next nav-next-testimonial">
                        <x-core::icon
                            name="ti ti-chevron-left"
                            class="icon"
                        />
                    </div>
                    <div class="navigation swiper-nav-prev nav-prev-testimonial">
                        <x-core::icon
                            name="ti ti-chevron-right"
                            class="icon"
                        />
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div
                    class="swiper tf-sw-testimonial"
                    data-preview-lg="2.6"
                    data-preview-md="2"
                    data-preview-sm="2"
                    data-space="30"
                    {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
                >
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <div
                                    class="box-tes-item wow fadeIn"
                                    data-wow-delay=".2s"
                                    data-wow-duration="2000ms"
                                >
                                    @include(Theme::getThemeNamespace('partials.shortcodes.testimonials.partials.content'))
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
