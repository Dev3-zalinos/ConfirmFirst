<section class="flat-section flat-categories" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="wrap-categories wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
            <div
                class="swiper tf-sw-categories"
                data-preview-lg="6"
                data-preview-md="4"
                data-preview-sm="3"
                data-space="30"
                {!! Theme::partial('shortcode-slider-attributes', compact('shortcode')) !!}
            >
                <div class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <a href="{{ $category->url }}" class="homeya-categories" title="{{ $category->name }}">
                                @php
                                    $iconImage = $category->getMetaData('icon_image', true);
                                    $icon = $category->getMetaData('icon', true);
                                @endphp
                                @if ($iconImage || $icon)
                                    <div class="icon-box">
                                        @if ($iconImage)
                                            {{ RvMedia::image($iconImage, $category->name, 'thumb') }}
                                        @elseif ($icon)
                                            {!! BaseHelper::renderIcon($icon) !!}
                                        @endif
                                    </div>
                                @endif
                                <div class="content text-center">
                                    <h6 class="line-clamp-1">{{ $category->name }}</h6>
                                    <p class="mt-4 text-variant-1">
                                        @if ($category->properties_count === 1)
                                            {{  __('1 Property') }}
                                        @else
                                            {{  __(':count Properties', ['count' => $category->properties_count]) }}
                                        @endif
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="box-navigation">
                <div class="navigation style-1 swiper-nav-next nav-next-category">
                    <x-core::icon name="ti ti-chevron-left" />
                </div>
                <div class="navigation style-1 swiper-nav-prev nav-prev-category">
                    <x-core::icon name="ti ti-chevron-right" />
                </div>
            </div>
        </div>
    </div>
</section>
