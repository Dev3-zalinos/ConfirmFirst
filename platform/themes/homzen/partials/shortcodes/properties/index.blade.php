@php
    $style = in_array($shortcode->style, range(1, 7)) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.properties.styles.style-$style"))
