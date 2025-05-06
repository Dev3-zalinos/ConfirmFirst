@php
    $currencies = get_all_currencies();
@endphp

@if (count($currencies) > 1)
    <div class="dropdown">
        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ get_application_currency()->title }}
        </a>
        <ul class="dropdown-menu">
            @foreach ($currencies as $currency)
                <li>
                    <a  @class(['dropdown-item', 'active' => get_application_currency_id() == $currency->id]) href="{{ route('public.change-currency', $currency->title) }}">
                        {{ $currency->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
