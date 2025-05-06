<section class="flat-section flat-agents-v2" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false]) !!}

        <div class="grid-2 gap-30 wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            @foreach($accounts as $account)
                <div class="box-agent style-2 hover-img">
                    <div class="box-img img-style">
                        {{ RvMedia::image($account->avatar_url, $account->name) }}
                        {!! Theme::partial('shortcodes.agents.partials.social-links', compact('account')) !!}
                    </div>
                    <div class="content">
                        <a href="{{ $account->url }}">
                            <h6 class="text-center">{{ $account->name }}</h6>
                        </a>
                        {!! Theme::partial('shortcodes.agents.partials.info', compact('account')) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
