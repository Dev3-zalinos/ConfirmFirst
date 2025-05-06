@php
    $style ??= 1;
@endphp

<ul class="list-star">
    <li class="icon icon-star"></li>
    <li class="icon icon-star"></li>
    <li class="icon icon-star"></li>
    <li class="icon icon-star"></li>
    <li class="icon icon-star"></li>
</ul>
@if($style !== 4)
    <p class="note body-1">
        {!! BaseHelper::clean($testimonial->content) !!}
    </p>
@else
    <h5>{!! BaseHelper::clean($testimonial->content) !!}</h5>
@endif
<div @class(['box-avt d-flex align-items-center gap-12', 'justify-content-center my-3' => $style === 4])>
    <div class="avatar avt-60 round">
        {{ RvMedia::image($testimonial->image, $testimonial->name) }}
    </div>
    <div class="info">
        <div class="h7 fw-7">{{ $testimonial->name }}</div>
        <p class="text-variant-1 mt-4">{{ $testimonial->company }}</p>
    </div>
</div>
