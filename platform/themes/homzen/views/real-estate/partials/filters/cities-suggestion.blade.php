<ul class="search-suggestion">
    @forelse ($cities as $city)
        <li class="search-suggestion-item">{{ $city->name }}, {{ $city->state->name }}</li>
    @empty
        <li class="text-muted text-center">{{ __('No cities found') }}</li>
    @endforelse
</ul>
