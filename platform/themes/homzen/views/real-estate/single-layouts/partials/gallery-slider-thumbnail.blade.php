<div class="single-property-gallery">
    <div class="position-relative">
        <div class="swiper sw-single">
            <div class="swiper-wrapper">
                @foreach($property->images as $image)
                    <div class="swiper-slide">
                        <div class="image-sw-single">
                            {{ RvMedia::image($image, $property->name) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="box-navigation">
            <div class="navigation swiper-nav-next nav-next-single">
                <x-core::icon name="ti ti-chevron-left" />
            </div>
            <div class="navigation swiper-nav-prev nav-prev-single">
                <x-core::icon name="ti ti-chevron-right" />
            </div>
        </div>
    </div>

    <div thumbsSlider="" class="swiper thumbs-sw-pagi">
        <div class="swiper-wrapper">
            @foreach($property->images as $image)
                <div class="swiper-slide">
                    <div class="img-thumb-pagi">
                        {{ RvMedia::image($image, $property->name, 'medium-square', attributes: ['style' => 'height: 150px']) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
