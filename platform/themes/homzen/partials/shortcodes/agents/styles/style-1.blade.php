<section class="flat-section flat-agents" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-{{ $shortcode->items_per_row ?: 4 }}">
            @foreach ($accounts as $account)
                <div class="box col">
                    <div class="box-agent hover-img wow fadeIn" data-wow-delay=".2s" data-wow-duration="2000ms">
                        <div class="box-img img-style mb-2">
                            {{ RvMedia::image($account->avatar_url, $account->name) }}
                            {!! Theme::partial('shortcodes.agents.partials.social-links', compact('account')) !!}
                        </div>
                        <div class="content">
                            <div class="info">
                                @if (\Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile())
                                    <h6>{{ $account->name }}</h6>
                                @else
                                    <a href="{{ $account->url }}">
                                        <h6 class="link">{{ $account->name }}</h6>
                                    </a>
                                @endif
                                {!! Theme::partial('shortcodes.agents.partials.info', compact('account')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
