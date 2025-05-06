@php
    Theme::set('breadcrumbEnabled', 'no');
    Theme::set('pageTitle', $account->name);
@endphp

<section class="agent-detail-section">
    <div class="agent-header">
        <div class="agent-avatar">
            {{ RvMedia::image($account->avatar_url, $account->name, 'medium-square') }}
        </div>
        <div class="agent-info">
            <h2 class="agent-name">{{ $account->name }}</h2>
            @if($account->company)
                <p class="agent-company">{!! BaseHelper::clean(__('Company Agent at :company', ['company' => "<strong>$account->company</strong>"])) !!}</p>
            @endif
            <div class="agent-contact-info">
                @if($account->phone && ! setting('real_estate_hide_agency_phone', false))
                    <a href="tel:{{ $account->phone }}" class="agent-info-item">
                        <x-core::icon name="ti ti-phone" />
                        {{ $account->phone }}
                    </a>
                @endif
                @if($account->email && ! setting('real_estate_hide_agency_email', false))
                    <a href="mailto:{{ $account->email }}" class="agent-info-item">
                        <x-core::icon name="ti ti-mail" />
                        {{ $account->email }}
                    </a>
                @endif
                <div class="agent-info-item">
                    <x-core::icon name="ti ti-calendar" />
                    {{ __('Joined') }} {{ $account->created_at->diffForHumans() }}
                </div>
            </div>

            {!! Theme::partial('shortcodes.agents.partials.social-links', compact('account')) !!}
        </div>
    </div>

    @if($account->description)
        <div class="agent-about-section">
            <h5>{{ __('About Agent') }}</h5>
            <p class="agent-description">{!! BaseHelper::clean($account->description) !!}</p>
        </div>
    @endif

    @if ($properties->isNotEmpty())
        <div class="agent-properties-section">
            <h5>{{ __('Properties by this agent') }}</h5>
            @include(Theme::getThemeNamespace('views.real-estate.properties.index'))
        </div>
    @endif

    {!! apply_filters('real_estate_agent_details', null, $account) !!}
</section>
