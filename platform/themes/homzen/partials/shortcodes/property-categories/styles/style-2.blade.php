@php
    $backgroundColor = ($shortcode->background_color && $shortcode->background_color != 'transparent') ? $shortcode->background_color : '#161e2d';
@endphp

<section class="flat-section-v3" style="background-color: {{ $backgroundColor }}">
    <div class="container">
        @if($shortcode->subtitle || $shortcode->title)
            <div class="box-title text-center wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                @if($shortcode->subtitle)
                    <div class="text-subtitle text-white">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                @endif
                @if($shortcode->title)
                    <h2 class="section-title mt-4 text-white">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                @endif
            </div>
        @endif
        <div
            class="swiper tf-sw-categories wow fadeInUpSmall"
            data-wow-delay=".4s"
            data-wow-duration="2000ms"
            data-preview-lg="5"
            data-preview-md="4"
            data-preview-sm="3"
            data-space="40"
            {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
        >
            <div class="swiper-wrapper">
                @foreach ($categories as $category)
                    <div class="swiper-slide">
                        <a href="{{ $category->url }}" class="homeya-categories style-1" title="{{ $category->name }}">
                            @php
                                $iconImage = $category->getMetaData('icon_image', true);
                                $icon = $category->getMetaData('icon', true);
                            @endphp
                            @if ($iconImage || $icon)
                                <div class="box-icon w-80 round">
                                    @if ($iconImage)
                                        {{ RvMedia::image($iconImage, $category->name, 'thumb') }}
                                    @elseif ($icon)
                                        {!! BaseHelper::renderIcon($icon) !!}
                                    @endif
                                </div>
                            @endif

                            <div class="content text-center">
                                <h6 class="line-clamp-1">{{ $category->name }}</h6>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="sw-pagination sw-pagination-category"></div>
        </div>
    </div>
</section>
