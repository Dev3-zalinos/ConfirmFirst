<section @class(['flat-section', 'text-center' => $shortcode->centered_content]) @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false]) !!}

        @if($services)
            <div @class(['wrap-service wow fadeInUpSmall', 'flat-service' => $counters]) data-wow-delay=".4s" data-wow-duration="2000ms">
                @foreach($services as $service)
                    <div class="box-service hover-btn-view">
                        <div class="icon-box">
                            @if($service['icon_image'])
                                {{ RvMedia::image($service['icon_image'], $service['title'], attributes: ['class' => 'icon', 'data-bb-lazy' => 'false', 'style' => sprintf('max-width: %spx !important; max-height: %spx !important;', $iconImageSize, $iconImageSize)]) }}
                            @elseif($service['icon'])
                                <x-core::icon :name="$service['icon']" class="icon" />
                            @endif
                        </div>
                        <div class="content">
                            <h6 class="title">{!! BaseHelper::clean($service['title']) !!}</h6>
                            <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                            @if($service['button_url'])
                                <a href="{{ $service['button_url'] }}" class="btn-view style-1">
                                    <span class="text">{{ $service['button_label'] ?? __('Learn More') }}</span>
                                    <x-core::icon name="ti ti-arrow-right" class="icon" style="stroke-width: 2" />
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {!! Theme::partial('shortcodes.services.partials.counters', compact('counters')) !!}
    </div>
</section>
