<section class="flat-slider home-2">
    <div class="container relative">
        <div class="row">
            <div class="col-xl-10">
                <div class="slider-content">
                    <div class="heading">
                        <h2 class="title wow fadeIn animationtext clip" data-wow-delay=".2s" data-wow-duration="2000ms">
                            {!! BaseHelper::clean($shortcode->title) !!}
                            <br>
                            {!! Theme::partial('shortcodes.hero-banner.partials.animation-text', compact('shortcode')) !!}
                        </h2>
                        @if ($shortcode->description)
                            <p class="subtitle body-1 wow fadeIn" data-wow-delay=".8s" data-wow-duration="2000ms">
                                {!! BaseHelper::clean($shortcode->description) !!}
                            </p>
                        @endif
                    </div>
                    {!! Theme::partial('shortcodes.hero-banner.partials.action-button', ['shortcode' => $shortcode, 'class' => 'mb-5']) !!}
                    @if(is_plugin_active('real-estate') && $shortcode->search_box_enabled)
                        @include(Theme::getThemeNamespace('views.real-estate.partials.search-box'), ['style' => 2, 'noLeftRound' => true])
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($shortcode->background_image)
        <div class="img-banner-left">
            {{ RvMedia::image($shortcode->background_image, $shortcode->title) }}
        </div>
    @endif

    <div class="img-banner-right">
        <div class="swiper slider-sw-home2">
            <div class="swiper-wrapper">
                @foreach (range(1, 4) as $i)
                    @continue(! $shortcode->{"slider_image_$i"})

                    <div class="swiper-slide">
                        <div class="slider-home2 img-animation wow">
                            {{ RvMedia::image($shortcode->{"slider_image_$i"}, $shortcode->title) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
