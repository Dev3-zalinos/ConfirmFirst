<section class="flat-section pt-0 flat-banner">
    <div class="container">
        <div class="wrap-banner bg-surface">
            <div class="box-left">
                <div class="box-title">
                    @if($shortcode->subtitle)
                        <div class="text-subtitle text-primary">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                    @endif
                    @if($shortcode->title)
                        <h2 class="section-title mt-4">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                    @endif
                </div>
                @if($shortcode->button_label)
                    <a href="{{ $shortcode->button_url }}" class="tf-btn primary size-1">
                        {{ $shortcode->button_label }}
                    </a>
                @endif
            </div>
            @if($shortcode->image)
                <div class="box-right">
                    {{ RvMedia::image($shortcode->image, $shortcode->title) }}
                </div>
            @endif
        </div>
    </div>
</section>
