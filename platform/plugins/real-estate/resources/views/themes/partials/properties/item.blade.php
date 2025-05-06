<div>
    <div class="archive-top">
        <a href="{{ $property->url }}" class="images-group">
            <div class="images-style">
                {{ RvMedia::image($property->image, $property->name) }}
            </div>
            <div class="top">
                <div class="d-flex gap-8">
                    @if($property->is_featured)
                        <span class="flag-tag success">{{ __('Featured') }}</span>
                    @endif
                    {!! $property->status->toHtml() !!}
                </div>
                <ul class="d-flex gap-4">
                    <li class="box-icon w-32">
                        <span class="icon icon-arrLeftRight"></span>
                    </li>
                    <li class="box-icon w-32">
                        <span class="icon icon-heart"></span>
                    </li>
                    <li class="box-icon w-32">
                        <span class="icon icon-eye"></span>
                    </li>
                </ul>
            </div>
            @if($property->category)
                <div class="bottom">
                    <span class="flag-tag style-2">{{ $property->category->name }}</span>
                </div>
            @endif
        </a>
        <div class="content">
            <div>
                <a href="{{ $property->url }}" class="link">{!! BaseHelper::clean($property->name) !!}</a>
            </div>
            @if($property->address)
                <div class="desc">
                    <i class="icon icon-mapPin"></i>
                    <p>{{ $property->address }}</p>
                </div>
            @endif
            <p class="note">{!! Str::limit(BaseHelper::clean($property->description)) !!}</p>
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
    </div>
    <div class="archive-bottom d-flex justify-content-between align-items-center">
        @if($author = $property->author)
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
</div>
