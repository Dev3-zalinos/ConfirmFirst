@if (theme_option('real_estate_enable_filter_by_amenities', 'yes') == 'yes')
    @php
        $features = \Botble\RealEstate\Models\Feature::query()
            ->wherePublished()
            ->get();

        $asGrid ??= true;
    @endphp

    <div class="group-checkbox form-search-amenities">
        <div class="text-1">{{ __('Amenities:') }}</div>
        <div @class(['group-amenities', 'mt-8' => $asGrid])>
            @if($asGrid)
                <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xl-6 box-amenities g-3">
            @endif
                @foreach($features as $feature)
                    @if($asGrid)
                    <div class="col">
                    @endif
                        <fieldset class="amenities-item">
                            <input type="checkbox" name="features[]" class="tf-checkbox style-1" id="features-{{ $feature->getKey() }}" value="{{ $feature->getKey() }}" @checked(in_array($feature->getKey(), request()->query('features', []))) />
                            <label for="features-{{ $feature->getKey() }}" class="text-cb-amenities">{{ $feature->name }}</label>
                        </fieldset>
                    @if($asGrid)
                    </div>
                    @endif
                @endforeach
            @if($asGrid)
                </div>
            @endif
        </div>
    </div>
@endif
