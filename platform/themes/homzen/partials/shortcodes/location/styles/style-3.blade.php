<section
    class="flat-section flat-location-v3"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="grid-location-v2 wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
            @foreach($locations as $location)
                <div class="box-location-v3 hover-img not-overlay hover-btn-view">
                    <div class="img-style">
                        {{ RvMedia::image($location->image, $location->name, 'medium-square', attributes: ['style' => 'max-width: 150px']) }}
                    </div>
                    <div class="content">
                        <h6>
                            <a href="{{ $location->url }}">{{ $location->name }}</a>
                        </h6>
                        <a href="{{ $location->url }}" class="btn-view style-1">
                            <span class="text">{{ __('Explore Now') }}</span>
                            <x-core::icon name="ti ti-arrow-right" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
