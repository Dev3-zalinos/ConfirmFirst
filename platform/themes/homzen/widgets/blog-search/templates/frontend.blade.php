<div class="widget-search">
    <div class="h7 fw-7 text-black">{{ __('Search') }}</div>
    <x-core::form :url="route('public.search')" method="GET" class="search-box">
        <input class="search-field" type="search" name="q" placeholder="{{ __('Search...') }}" value="{{ BaseHelper::stringify(request()->query('q')) }}" />
        <button type="submit" class="right-icon" title="{{ __('Search') }}">
            <x-core::icon name="ti ti-search" />
        </button>
    </x-core::form>
</div>
