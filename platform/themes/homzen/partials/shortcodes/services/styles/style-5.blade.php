<section class="flat-benefit-v2">
    <div class="container">
        <div class="row wrap-benefit-v2">
            <div class="col-lg-4 wow fadeIn" data-wow-delay=".2s" data-wow-duration="2000ms">
                <div class="box-left">
                    @if($shortcode->title || $shortcode->subtitle)
                        <div class="box-title">
                            @if($shortcode->subtitle)
                                <div class="text-subtitle text-primary">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                            @endif
                            @if($shortcode->title)
                                <h2 class="section-title mt-4 text-white">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                            @endif
                        </div>
                    @endif
                    @if($shortcode->description)
                        <p class="description text-white body-3">{!! BaseHelper::clean($shortcode->description) !!}</p>
                    @endif
                    <div class="box-navigation">
                        <div class="navigation swiper-nav-next nav-next-benefit">
                            <x-core::icon name="ti ti-chevron-left" />
                        </div>
                        <div class="navigation swiper-nav-prev nav-prev-benefit">
                            <x-core::icon name="ti ti-chevron-right" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 wow fadeIn" data-wow-delay=".4s" data-wow-duration="2000ms">
                <div class="swiper tf-sw-benefit">
                    <div class="swiper-wrapper">
                        @foreach(collect($services)->chunk(4) as $services)
                            <div class="swiper-slide">
                                <div class="box-right">
                                    @foreach($services as $service)
                                        <div class="box-benefit style-1">
                                            <div class="icon-box">
                                                @if ($service['icon_image'])
                                                    {{ RvMedia::image($service['icon_image'], $service['title'], attributes: ['class' => 'icon', 'data-bb-lazy' => 'false', 'style' => sprintf('max-width: %spx !important; max-height: %spx !important;', $iconImageSize, $iconImageSize)]) }}
                                                @elseif($service['icon'])
                                                    <x-core::icon :name="$service['icon']" />
                                                @endif
                                            </div>
                                            <div class="content">
                                                <h6 class="title link">{!! BaseHelper::clean($service['title']) !!}</h6>
                                                <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
