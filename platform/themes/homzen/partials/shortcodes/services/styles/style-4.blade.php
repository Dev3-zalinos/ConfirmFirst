<section class="flat-section flat-service-v4" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        <div class="wrap-service-v4 wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
            @if($shortcode->background_image)
                <div class="inner-service-left">
                    <div class="img-service">
                        {{ RvMedia::image($shortcode->background_image, $shortcode->name) }}
                    </div>
                </div>
            @endif
            <div class="inner-service-right">
                @if($shortcode->title || $shortcode->description || $shortcode->subtitle)
                    <div class="box-title">
                        @if($shortcode->subtitle)
                            <div class="text-subtitle text-primary">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                        @endif
                        @if($shortcode->title)
                            <h2 class="section-title mt-4">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                        @endif
                        @if($shortcode->description)
                            <p class="desc">{!! BaseHelper::clean($shortcode->description) !!}</p>
                        @endif
                    </div>
                @endif
                <ul class="list-service">
                    @foreach ($services as $service)
                        <li class="box-service hover-btn-view style-4">
                            <div class="icon-box">
                                @if ($service['icon_image'])
                                    {{ RvMedia::image($service['icon_image'], $service['title'], attributes: ['class' => 'icon', 'data-bb-lazy' => 'false', 'style' => sprintf('max-width: %spx !important; max-height: %spx !important;', $iconImageSize, $iconImageSize)]) }}
                                @elseif($service['icon'])
                                    <x-core::icon :name="$service['icon']" />
                                @endif
                            </div>
                            <div class="content">
                                <h6 class="title">{!! BaseHelper::clean($service['title']) !!}</h6>
                                <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                                @if ($service['button_url'])
                                    <a
                                        href="{{ $service['button_url'] }}"
                                        class="btn-view style-1"
                                    >
                                        <span class="text">{{ $service['button_label'] ?? __('Learn More') }}</span>
                                        <x-core::icon name="ti ti-arrow-right" />
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
