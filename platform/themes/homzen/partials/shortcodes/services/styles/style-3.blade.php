<section
    class="flat-section-v3 flat-service-v2"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        <div class="row wrap-service-v2">
            <div class="col-lg-6">
                <div class="box-left">
                    {!! Theme::partial('shortcode-heading', [
                        'shortcode' => $shortcode,
                        'centered' => false,
                        'animation' => false,
                        'hasButton' => false,
                    ]) !!}

                    @if ($shortcode->description)
                        <p>{!! BaseHelper::clean(nl2br($shortcode->description)) !!}</p>
                    @endif

                    <ul class="list-view">
                        @php
                            $checklist = array_filter(explode(',', ($shortcode->checklist ?: '')));
                        @endphp

                        @foreach ($checklist as $item)
                            <li>
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 16 16"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M8 15.9947C12.4183 15.9947 16 12.4154 16 8C16 3.58462 12.4183 0.00524902 8 0.00524902C3.58172 0.00524902 0 3.58462 0 8C0 12.4154 3.58172 15.9947 8 15.9947Z"
                                        fill="#198754"
                                    />
                                    <path
                                        d="M7.35849 12.2525L3.57599 9.30575L4.65149 7.9255L6.97424 9.735L10.8077 4.20325L12.2462 5.19975L7.35849 12.2525Z"
                                        fill="white"
                                    />
                                </svg>
                                {!! BaseHelper::clean($item) !!}
                            </li>
                        @endforeach
                    </ul>

                    @if ($shortcode->button_label && $shortcode->button_url)
                        <a
                            href="{{ $shortcode->button_url }}"
                            class="btn-view"
                        >
                            <span class="text">{{ $shortcode->button_label }}</span>
                            <x-core::icon
                                name="ti ti-arrow-right"
                                style="stroke-width: 2"
                            />
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="box-right">
                    @foreach ($services as $service)
                        <div class="box-service style-1 hover-btn-view">
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
                                        class="btn-view style-1"
                                    >
                                        <span class="text">{{ $service['button_label'] ?? __('Learn More') }}</span>
                                        <x-core::icon
                                            name="ti ti-arrow-right"
                                            style="stroke-width: 2"
                                        />
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
