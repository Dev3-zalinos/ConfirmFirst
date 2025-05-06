<section class="flat-section pt-0 flat-testimonial-v3 wow fadeInUpSmall" data-wow-delay=".6s" data-wow-duration="2000ms">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="box-test-left">
                    <div class="img-style">
                        @if($shortcode->background_image)
                            {{ RvMedia::image($shortcode->background_image, $shortcode->title) }}
                        @endif
                        @if($shortcode->subtitle || $shortcode->title)
                            <div class="title">
                                @if($shortcode->subtitle)
                                    <div class="text-subtitle text-primary">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                                @endif
                                @if($shortcode->title)
                                    <h2 class="section-title fw-6 text-white">
                                        {{ $shortcode->title }}
                                    </h2>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="content-box">
                        @if($shortcode->description)
                            <p class="body-2 text-white">{!! BaseHelper::clean($shortcode->description) !!}</p>
                        @endif
                        @if($shortcode->button_label && $shortcode->button_url)
                            <a href="{{ $shortcode->button_url }}" class="tf-btn primary size-1">{{ $shortcode->button_label }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="box-test-right">
                    <div
                        class="swiper tf-sw-testimonial"
                        data-preview-lg="1"
                        data-preview-md="1"
                        data-preview-sm="1"
                        data-space="30"
                        {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
                    >
                        <div class="swiper-wrapper">
                            @foreach ($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="box-tes-item-v2">
                                        @include(Theme::getThemeNamespace('partials.shortcodes.testimonials.partials.content'), ['style' => 4])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="sw-pagination sw-pagination-testimonial"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
