@if ($property->images)
    <section class="flat-gallery-single">
        @foreach((array) $property->images as $image)
            @if($loop->first)
                <div class="item1 box-img">
                    {{ RvMedia::image($image, $property->name) }}
                    <div class="box-btn">
                        <a href="{{ RvMedia::getImageUrl($image) }}" data-fancybox="gallery" class="tf-btn primary">{{ __('View All Photos (:count)', ['count' => count($property->images)]) }}</a>
                    </div>
                </div>
            @else
                <a href="{{ RvMedia::getImageUrl($image) }}" class="item-{{ $loop->iteration }} box-img" data-fancybox="gallery" @style(['display: none' => $loop->iteration > 5])>
                    {{ RvMedia::image($image, $property->name, lazy: false) }}
                </a>
            @endif
        @endforeach
    </section>
@endif
