<section
    class="flat-section flat-recommended wow fadeInUpSmall"
    data-wow-delay=".4s"
    data-wow-duration="2000ms"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'hasButton' => false]) !!}

        @include(Theme::getThemeNamespace('views.real-estate.properties.grid'), ['itemsPerRow' => 4])

        @if($shortcode->button_label && $shortcode->button_url)
            <div class="text-center">
                <a href="{{ $shortcode->button_url }}" class="tf-btn primary size-1">
                    {{ $shortcode->button_label }}
                </a>
            </div>
        @endif
    </div>
</section>
