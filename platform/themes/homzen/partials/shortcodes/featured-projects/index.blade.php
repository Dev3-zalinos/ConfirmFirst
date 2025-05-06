<section class="flat-section-v5 bg-surface flat-recommended flat-recommended-v2">
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'hasButton' => false, 'class' => 'style-2']) !!}

        @if ($projects->isNotEmpty())
            <div class="row wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                @foreach($projects as $project)
                    <div class="col-xl-4 col-md-6">
                        @include(Theme::getThemeNamespace('views.real-estate.projects.item-grid'))
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
