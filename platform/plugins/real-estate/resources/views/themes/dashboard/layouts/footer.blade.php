@if (($currencies = get_all_currencies()) && $currencies->count() > 1)
    <footer>
        <p class="inline-block">{{ __('Currencies') }}:
            @foreach ($currencies as $currency)
                <a
                    href="{{ route('public.change-currency', $currency->title) }}"
                    @if (get_application_currency_id() == $currency->id) class="active" @endif
                ><span>{{ $currency->title }}</span></a>
                @if (!$loop->last)
                    -
                @endif
            @endforeach
        </p>
    </footer>
@endif

<script src="{{ asset('vendor/core/plugins/real-estate/js/app.js') }}"></script>
