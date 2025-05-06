@php
    $animation ??= true;
    $centered ??= true;
    $buttonLabel ??= $shortcode->button_label;
    $buttonUrl ??= $shortcode->button_url;
    $hasButton ??= $buttonLabel && $buttonUrl;
@endphp

@if($shortcode->title || $shortcode->subtitle)
    <div
        @class(['box-title', 'text-center' => $centered && ! $hasButton, 'wow fadeIn' => $animation, 'style-1' => $hasButton, $class ?? null])
        @if($animation)
            data-wow-delay=".2s" data-wow-duration="2000ms"
        @endif
    >
        @if($hasButton)
            <div class="box-left">
        @endif
        @if($shortcode->subtitle)
            <div class="text-subtitle text-primary">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
        @endif
        @if($shortcode->title)
            <h2 class="section-title mt-4">{!! BaseHelper::clean($shortcode->title) !!}</h2>
        @endif
        @if($hasButton)
            </div>

            <a href="{{ $buttonUrl }}" class="btn-view">
                <span class="text">{{ $buttonLabel }}</span>
                <x-core::icon name="ti ti-arrow-right" class="icon" style="stroke-width: 2" />
            </a>
        @endif
    </div>
@endif
