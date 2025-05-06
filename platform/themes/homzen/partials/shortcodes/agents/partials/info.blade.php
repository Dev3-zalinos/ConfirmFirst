<ul class="list-info">
    @if ($account->properties_count)
        <li>
            <x-core::icon name="ti ti-home" />
            @if ($account->properties_count === 1)
                {{ __('1 Property') }}
            @else
                {{ __(':count Properties', ['count' => $account->properties_count]) }}
            @endif
        </li>
    @endif

    @if ($account->phone && ! setting('real_estate_hide_agency_phone', 0))
        <li>
            <a href="tel:{{ $account->phone }}"><x-core::icon name="ti ti-phone" /> {{ $account->phone }}</a>
        </li>
    @endif

        @if ($account->email && ! setting('real_estate_hide_agency_email', 0))
        <li>
            <a href="mailto:{{ $account->email }}"><x-core::icon name="ti ti-mail" /> {{ $account->email }}</a>
        </li>
    @endif

        @if ($account->address)
        <li><x-core::icon name="ti ti-map-pin" /> {{ $account->address }}</li>
    @endif
</ul>
