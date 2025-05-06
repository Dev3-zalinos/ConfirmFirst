@php

$accountSocials = $account->getMetaData('social_links', true);

if ($accountSocials !== null && $accountSocials !== '[]') {
    $socials = Theme::convertSocialLinksToArray($accountSocials);
} else {
    $socials = [];
}
@endphp

@if ($socials)
    <ul class="agent-social">
        @foreach($socials as $social)
            @continue((! $social->getIcon() && ! $social->getImage()) || ! $social->getUrl())

            <li>
                <a href="{{ $social->getUrl() }}" target="_blank" rel="noopener noreferrer">
                    @if ($social->getImage())
                        {!! $social->getImageHtml() !!}
                    @else
                        {!! $social->getIconHtml() !!}
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
@endif
