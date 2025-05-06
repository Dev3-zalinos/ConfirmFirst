@if ($model->images && is_array($model->images))
    <section class="flat-location flat-slider-detail-v1">
        <div class="swiper tf-sw-location" data-preview-lg="2.03" data-preview-md="2" data-preview-sm="2" data-space="20" data-centered="true" data-loop="true">
            <div class="swiper-wrapper">
                @foreach ($model->images as $image)
                    <div class="swiper-slide">
                        <a href="{{ RvMedia::getImageUrl($image) }}" data-fancybox="gallery" class="box-imgage-detail d-block">
                            {{ RvMedia::image($image, $model->name, attributes: ['width' => '100%'], lazy: false) }}
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="box-navigation">
                <div class="navigation swiper-nav-next nav-next-location">
                    <x-core::icon name="ti ti-chevron-left" class="icon" />
                </div>
                <div class="navigation swiper-nav-prev nav-prev-location">
                    <x-core::icon name="ti ti-chevron-right" class="icon" />
                </div>
            </div>
        </div>
    </section>
@endif
