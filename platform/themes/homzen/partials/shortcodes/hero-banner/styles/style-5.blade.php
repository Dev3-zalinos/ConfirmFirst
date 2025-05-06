@php
    $images = [$shortcode->background_image];

    $title = $shortcode->title;

    if ($property) {
        $images = $property->images;
        $title = $property->name;
    }

    if ($shortcode->transparent_header) {
        Theme::set('headerClass', 'header-fixed header-style-2');
    }
@endphp

<section class="flat-slider home-5">
    <div class="wrap-slider-swiper">
        <div class="swiper-container thumbs-swiper-column">
            <div class="swiper-wrapper">
                @foreach($images as $image)
                    <div class="swiper-slide">
                        <div class="box-img">
                            {{ RvMedia::image($image, $title) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="swiper-container thumbs-swiper-column1 swiper-pagination5">
            <div class="swiper-wrapper">
                @foreach($images as $image)
                    <div class="swiper-slide">
                        <div class="image-detail">
                            {{ RvMedia::image($image, $title, 'thumb') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="info-box">
                    @if ($property)
                    <div class="box-top">
                        <div class="d-flex gap-8">
                            @if($property->is_featured)
                                <span class="flag-tag success">{{ __('Featured') }}</span>
                            @endif
                            {!! BaseHelper::clean($property->status->toHtml()) !!}
                        </div>
                        <h6 class="title"><a href="{{ $property->url }}">{!! BaseHelper::clean($property->name) !!}</a></h6>
                        @if($property->address)
                            <div class="desc">
                                <i class="fs-16 icon icon-mapPin"></i>
                                <p>{{ $property->address }}</p>
                            </div>
                        @endif
                        <ul class="meta-list">
                            @if($property->number_bedroom)
                                <li class="item">
                                    <i class="icon icon-bed"></i>
                                    <span>{{ number_format($property->number_bedroom) }}</span>
                                </li>
                            @endif
                            @if($property->number_bathroom)
                                <li class="item">
                                    <i class="icon icon-bathtub"></i>
                                    <span>{{ number_format($property->number_bathroom) }}</span>
                                </li>
                            @endif
                            @if($property->square)
                                <li class="item">
                                    <i class="icon icon-ruler"></i>
                                    <span>{{ $property->square_text }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="box-bottom">
                        @if (($author = $property->author) && $property->author->name)
                            <div class="d-flex gap-8 align-items-center">
                                <div class="avatar avt-40 round">
                                    {{ RvMedia::image($author->avatar_url, $author->name, 'thumb') }}
                                </div>
                                <span>{{ $author->name }}</span>
                            </div>
                        @endif
                        <div class="d-flex align-items-center">
                            <h6>{{ format_price($property->price, $property->currency) }}</h6>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if(is_plugin_active('real-estate') && $shortcode->search_box_enabled)
    <section class="flat-filter-search home-5">
        <div class="container">
            @include(Theme::getThemeNamespace('views.real-estate.partials.search-box'), ['style' => 4, 'noLeftRound' => true])
        </div>
    </section>
@endif
