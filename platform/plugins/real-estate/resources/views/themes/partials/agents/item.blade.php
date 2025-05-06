<div class="agents-grid">

    <div class="agents-grid-wrap">

        <div class="fr-grid-thumb">
            @if ($account->url)
                <a href="{{ $account->url }}">
                    <img src="{{ $account->avatar_url ?: RvMedia::getImageUrl($account->avatar->url, 'medium') }}" class="img-fluid mx-auto" alt="{{ $account->name }}">
                </a>
            @else
                <span>
                    <img src="{{ $account->avatar_url ?: RvMedia::getImageUrl($account->avatar->url, 'medium') }}" class="img-fluid mx-auto" alt="{{ $account->name }}">
                </span>
            @endif
        </div>

        <div class="fr-grid-detail">
            <div class="fr-grid-detail-flex">
                <h5 class="fr-can-name">
                    @if ($account->url)
                        <a href="{{ $account->url }}">{{ $account->name }}</a>
                    @else
                        {{ $account->name }}
                    @endif
                </h5>
            </div>
            @if ($account->email && ! setting('real_estate_hide_agency_email', 0))
                <div class="fr-grid-detail-flex-right">
                    <div class="agent-email"><a href="mailto:{{ $account->email }}" title="{{ $account->name }}"><i class="fa fa-envelope"></i></a></div>
                </div>
            @endif
        </div>

    </div>

    <div class="fr-grid-info">
        <ul>
            @if ($account->phone && ! setting('real_estate_hide_agency_phone', 0))
                <li><strong class="d-inline-block me-1">{{ __('Phone') }}:</strong>&nbsp;<span dir="ltr">{{ $account->phone }}</span></li>
            @endif

            @if ($account->email && ! setting('real_estate_hide_agency_email', 0))
                <li><strong class="d-inline-block me-1">{{ __('Email') }}:</strong>&nbsp;<span dir="ltr">{{ $account->email }}</span></li>
            @endif
        </ul>
    </div>

    <div class="fr-grid-footer">
        <div class="fr-grid-footer-flex">
            <span class="fr-position"><i class="fa fa-home"></i>&nbsp;{{ $account->properties_count == 1 ? __(':count property', ['count' => $account->properties_count]) : __(':count properties', ['count' => $account->properties_count]) }}</span>
        </div>
        @if ($account->url)
            <div class="fr-grid-footer-flex-right">
                <a href="{{ $account->url }}" class="prt-view" tabindex="0">{{ __('View') }}</a>
            </div>
        @endif
    </div>

</div>
