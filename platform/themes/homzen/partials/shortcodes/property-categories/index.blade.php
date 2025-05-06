@php
    $style = in_array($shortcode->style, [1, 2, 3]) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.property-categories.styles.style-$style"))
