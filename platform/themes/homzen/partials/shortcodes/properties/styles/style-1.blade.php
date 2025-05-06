<section
    class="flat-section flat-property"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false]) !!}

        @php
            $firstProperty = $properties->first();
        @endphp

        <div class="wrap-property row row-cols-lg-2 g-4">
            @if ($firstProperty)
                <div class="box-left wow fadeInLeftSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                    @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'), ['property' => $firstProperty, 'class' => 'lg'])
                </div>
            @endif

            @if ($properties->count() > 1)
                <div class="box-right wow fadeInRightSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                    @foreach($properties->skip(1) as $property)
                        @include(Theme::getThemeNamespace('views.real-estate.properties.item-list'))
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
