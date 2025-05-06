<section class="flat-section-v5 bg-surface flat-recommended flat-recommended-v2">
    <div class="container">
         {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'hasButton' => false, 'class' => 'style-2']) !!}

        @if ($properties->isNotEmpty())
            <div class="row wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                @foreach($properties as $property)
                    <div class="col-xl-4 col-md-6">
                        @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'))
                    </div>
                @endforeach
            </div>
        @endif
        @if ($shortcode->button_label && $shortcode->button_url)
            <div class="text-center">
                <a href="{{ $shortcode->button_url }}" class="tf-btn primary size-1">
                    {{ $shortcode->button_label }}
                </a>
            </div>
        @endif
    </div>
</section>
