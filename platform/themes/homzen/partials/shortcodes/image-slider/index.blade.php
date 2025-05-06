<section class="flat-section-v4 flat-partner" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        <div
            class="wrap-partner swiper tf-sw-partner"
            data-preview-lg="5"
            data-preview-md="4"
            data-preview-sm="3"
            data-space="80"
            {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
        >
            <div class="swiper-wrapper">
                @foreach($tabs as $tab)
                    @continue(! $tab['image'])

                    <div class="swiper-slide">
                        <div  class="partner-item">
                            @if($tab['url'])
                                <a href="{{ $tab['url']}}" @if($tab['open_in_new_tab'] ?? false) target="_blank" @endif>
                                    {{ RvMedia::image($tab['image'], $tab['name']) }}
                                </a>
                            @else
                                {{ RvMedia::image($tab['image'], $tab['name']) }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
