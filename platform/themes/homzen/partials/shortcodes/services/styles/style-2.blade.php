<section
    class="flat-section flat-service-v3"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode]) !!}

        @if ($services)
            <div @class(['row', 'flat-service' => $counters])>
                @foreach ($services as $service)
                    <div class="box col-lg-4 col-md-6">
                        <div class="box-service style-2">
                            <div class="icon-box">
                                @if ($service['icon_image'])
                                    {{ RvMedia::image($service['icon_image'], $service['title'], attributes: ['class' => 'icon', 'data-bb-lazy' => 'false', 'style' => sprintf('max-width: %spx !important; max-height: %spx !important;', $iconImageSize, $iconImageSize)]) }}
                                @elseif($service['icon'])
                                    <x-core::icon
                                        :name="$service['icon']"
                                        class="icon"
                                    />
                                @endif
                            </div>
                            <div class="content">
                                <h6 class="title">{!! BaseHelper::clean($service['title']) !!}</h6>
                                <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                                @if ($service['button_url'])
                                    <a
                                        href="{{ $service['button_url'] }}"
                                        class="tf-btn size-1"
                                    >
                                        <span class="text">{{ $service['button_label'] ?? __('Learn More') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {!! Theme::partial('shortcodes.services.partials.counters', compact('counters')) !!}
    </div>
</section>
