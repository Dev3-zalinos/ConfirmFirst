@php
    $style = in_array($shortcode->style, range(1, 5)) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.hero-banner.styles.style-$style"))
