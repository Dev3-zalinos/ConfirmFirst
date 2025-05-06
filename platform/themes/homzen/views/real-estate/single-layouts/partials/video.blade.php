@php
    use Botble\Theme\Supports\Youtube;

    $videoUrl = $property->getMetaData('video_url', true);

    $videoThumbnail = $property->getMetaData('video_thumbnail', true);

    $isYouTubeVideo = Youtube::isYoutubeURL($videoUrl);

    if ($isYouTubeVideo) {
        $videoUrl = Youtube::getYoutubeVideoEmbedURL($videoUrl);

        if (! $videoThumbnail) {
            $videoThumbnail = Youtube::getThumbnail($videoUrl);
        }
    }
@endphp

@if ($videoUrl)
    <div @class(['single-property-video', $class ?? null])>
        <div class="h7 title fw-7">{{ __('Video') }}</div>
        <div class="img-video">
            <img src="{{ RvMedia::getImageUrl($videoThumbnail) }}" alt="{{ $property->name }}">
            <a href="{{ $videoUrl }}" @if ($isYouTubeVideo) data-fancybox="gallery2" @endif class="btn-video">
                <x-core::icon name="ti ti-player-play-filled" />
            </a>
        </div>
    </div>
@endif
