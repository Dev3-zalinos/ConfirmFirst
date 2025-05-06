@php
    $style = in_array($shortcode->style, [1, 2]) ? $shortcode->style : 1;

    Theme::asset()->container('footer')->remove('contact-public-js');
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.contact-form.styles.style-$style"))
