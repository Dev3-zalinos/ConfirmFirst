<section
    class="flat-section-v3 flat-location flat-location-v2"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            <div
                class="swiper tf-sw-location"
                data-preview-lg="4"
                data-preview-md="3"
                data-preview-sm="2"
                data-space="30"
                data-centered="false"
                {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
            >
                <div class="swiper-wrapper">
                    @foreach($locations as $location)
                        <div class="swiper-slide">
                            <a href="{{ $location->url }}" class="box-location style-1">
                                <div class="image">
                                    {{ RvMedia::image($location->image, $location->name, 'medium-rectangle-column') }}
                                </div>
                                <div class="content">
                                    <h6 class="title line-clamp-1">{{ $location->name }}</h6>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="box-navigation">
        <div class="navigation swiper-nav-next nav-next-location">
            <x-core::icon name="ti ti-chevron-left" />
        </div>
        <div class="navigation swiper-nav-prev nav-prev-location">
            <x-core::icon name="ti ti-chevron-right" />
        </div>
    </div>
</section>
