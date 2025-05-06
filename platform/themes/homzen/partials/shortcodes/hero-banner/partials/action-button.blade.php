@if($shortcode->button_label && $shortcode->button_url)
    <a href="{{ $shortcode->button_url }}" @class(['tf-btn primary size-2', $class ?? null])>
        {{ $shortcode->button_label }}
    </a>
@endif
