<section class="flat-section-v3 flat-latest-new" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        @include(Theme::getThemeNamespace('views.blog.partials.posts'))
    </div>
</section>
