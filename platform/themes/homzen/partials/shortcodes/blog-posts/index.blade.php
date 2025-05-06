@php
    $style = in_array($shortcode->style, range(1, 2)) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.blog-posts.styles.style-$style"))
