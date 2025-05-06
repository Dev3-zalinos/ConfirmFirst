<section
    class="flat-section flat-location-v2"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="grid-location wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            @foreach($locations as $location)
                <a href="{{ $location->url }}" class="item-{{ $loop->iteration }} box-location-v2 hover-img">
                    <div class="box-img img-style">
                        {{ RvMedia::image($location->image, $location->name, 'medium-rectangle-column') }}
                    </div>
                    <div class="content">
                        <h6 class="link">{{ $location->name }}</h6>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
