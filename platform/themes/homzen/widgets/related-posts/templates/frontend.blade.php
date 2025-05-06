@if ($posts->isNotEmpty())
    <section class="flat-section flat-latest-post">
        <div class="container">
            @if($config['title'] || $config['subtitle'])
                <div class="box-title-relatest text-center">
                    @if($config['subtitle'])
                        <div class="text-subheading text-primary">{!! BaseHelper::clean($config['subtitle']) !!}</div>
                    @endif
                    @if($config['title'])
                        <h5 class="mt-4">{!! BaseHelper::clean($config['title']) !!}</h5>
                    @endif
                </div>
            @endif

            @if(Theme::get('currentPostId'))
                @include(Theme::getThemeNamespace('views.blog.partials.posts'), compact('posts'))
            @endif
        </div>
    </section>
@endif
