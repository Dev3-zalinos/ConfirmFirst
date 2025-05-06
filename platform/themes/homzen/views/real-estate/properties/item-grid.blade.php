@php
    $class ??= null;
    $itemsPerRow ??= 3;
@endphp

<div @class(['property-item homeya-box', $class]) @if ($property->latitude && $property->longitude) data-lat="{{ $property->latitude }}" data-lng="{{ $property->longitude }}" @endif>
    <div class="archive-top">
        <a href="{{ $property->url }}" class="images-group">
            <div class="images-style">
                {{ RvMedia::image($property->image, $property->name, 'medium-rectangle') }}
            </div>
            <div class="top">
                <div class="d-flex gap-8">
                    @if($property->is_featured)
                        <span class="flag-tag success">{{ __('Featured') }}</span>
                    @endif
                    {!! BaseHelper::clean($property->status->toHtml()) !!}
                </div>
                @if (RealEstateHelper::isEnabledWishlist())
                    <div class="d-flex gap-4">
                        <button type="button" class="box-icon w-32"
                                data-type="property"
                                data-bb-toggle="add-to-wishlist"
                                data-id="{{ $property->getKey() }}"
                                data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                                data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}"
                        >
                            <x-core::icon name="ti ti-heart" />
                        </button>
                    </div>
                @endif
            </div>
            @if($property->category)
                <div class="bottom">
                    <span class="flag-tag style-2">{{ $property->category->name }}</span>
                </div>
            @endif
        </a>
        <div class="content">
            <{{ $class === 'lg' ? 'h5' : 'div' }} @class(['text-capitalize', 'h7 fw-7' => $class !== 'lg'])>
                <a href="{{ $property->url }}" class="link line-clamp-1" title="{{ $property->name }}">{!! BaseHelper::clean($property->name) !!}</a>
            </{{ $class === 'lg' ? 'h5' : 'div' }}>
            @if($property->short_address)
                <div class="desc">
                    <i class="icon icon-mapPin"></i>
                    <p class="line-clamp-1">{{ $property->short_address }}</p>
                </div>
            @endif
            @if($class === 'lg' && $property->description)
                <p class="note">{!! Str::limit(BaseHelper::clean($property->description)) !!}</p>
            @endif
            <ul class="meta-list">
                @if($property->number_bedroom !== null)
                    <li class="item">
                        <i class="icon icon-bed"></i>
                        <span>{{ $property->number_bedroom === 0 ? __('Studio') : number_format($property->number_bedroom) }}</span>
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
    </div>
    <div class="archive-bottom d-flex justify-content-between align-items-center">
        @if (! \Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile() && ($author = $property->author) && $property->author->name)
            <div class="d-flex gap-8 align-items-center">
                @if ($itemsPerRow < 4)
                    <div class="avatar avt-40 round">
                        {{ RvMedia::image($author->avatar_url, $author->name, 'thumb') }}
                    </div>
                @endif
                <span>{{ $author->name }}</span>
            </div>
        @endif
        <div class="d-flex align-items-center">
            <h6>{{ format_price($property->price, $property->currency) }}</h6>
        </div>
    </div>
</div>
