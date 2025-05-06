@if (is_plugin_active('location'))
    @if (theme_option('real_estate_use_location_in_search_box_as_dropdown', 'no') === 'yes')
        <div class="form-group-2 form-style form-search-location">
            <label>{{ __('Location') }}</label>
            <div class="position-relative">
                <div class="group-select">
                    <select name="city_id" id="location" class="select_js">
                        <option value="">{{ __('All') }}</option>
                        @if (request()->query('city_id'))
                            @php
                                $selectedCity = \Botble\Location\Models\City::query()
                                    ->wherePublished()
                                    ->where('id', request()->query('city_id'))
                                    ->first();
                            @endphp
                            @if ($selectedCity)
                                <option value="{{ $selectedCity->getKey() }}" selected>{{ $selectedCity->name }}</option>
                            @endif
                        @endif
                    </select>
                </div>
            </div>
        </div>
    @else
        <div class="form-group-2 form-style form-search-location" data-bb-toggle="search-suggestion">
            <label>{{ __('Location') }}</label>
            <div class="position-relative">
                <div @class(['group-ip', 'ip-icon' => $style === 3])>
                    <input type="text" class="form-control" placeholder="{{ __('Search for Location') }}" value="{{ BaseHelper::stringify(request()->query('location')) }}" name="location" data-url="{{ route('public.ajax.cities') }}" />
                    <x-core::icon name="ti ti-current-location" @class(['icon-right icon-location' => $style === 3]) />
                </div>
                <div data-bb-toggle="data-suggestion"></div>
            </div>
        </div>
    @endif
@endif
