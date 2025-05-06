<section
    class="flat-section flat-property-v2"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false]) !!}

        <div class="grid-2 gap-30 wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            @foreach ($properties as $property)
                @include(Theme::getThemeNamespace("views.real-estate.properties.item-list"))
            @endforeach
        </div>
    </div>
</section>
