<section
    class="flat-section-v3 flat-location"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container-full">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            <div
                class="swiper tf-sw-location overlay"
                data-preview-lg="4.1"
                data-preview-md="3"
                data-preview-sm="2"
                data-space="30"
                data-centered="true"
                {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
            >
                <div class="swiper-wrapper">
                    @foreach($locations as $location)
                        <div class="swiper-slide">
                            <a href="{{ $location->url }}" class="box-location">
                                <div class="image">
                                    {{ RvMedia::image($location->image, $location->name) }}
                                </div>
                                <div class="content">
                                    <h6 class="title">{{ $location->name }}</h6>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="box-navigation">
                    <div class="navigation swiper-nav-next nav-next-location">
                        <x-core::icon name="ti ti-chevron-left" class="icon" />
                    </div>
                    <div class="navigation swiper-nav-prev nav-prev-location">
                        <x-core::icon name="ti ti-chevron-right" class="icon" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
