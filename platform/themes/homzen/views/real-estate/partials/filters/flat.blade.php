@if (theme_option('real_estate_enable_filter_by_flat', 'yes') == 'yes')
    @php
        $minFlat = get_min_flat();
        $maxFlat = get_max_flat();
    @endphp

    @if ($maxFlat > $minFlat)
        <div @class(['widget-price form-search-flat', $class ?? null])>
            <div class="box-title-price">
                <span class="title-price">{{ __('Flat Range') }}</span>
                <div class="caption-price">
                    <span>{{ __('from') }}</span>
                    <span id="slider-flat-value01" class="fw-7 ms-1 me-1"></span>
                    <span>{{ __('to') }}</span>
                    <span id="slider-flat-value02" class="fw-7 ms-1"></span>
                </div>
            </div>
            <div id="slider-flat"
                 data-min="{{ get_min_flat() }}"
                 data-max="{{ get_max_flat() }}"
            ></div>
            <div class="slider-labels">
                <div>
                    <input type="hidden" name="min_flat" value="{{ BaseHelper::stringify(request()->query('min_flat')) }}" />
                    <input type="hidden" name="max_flat" value="{{ BaseHelper::stringify(request()->query('max_flat')) }}" />
                </div>
            </div>
        </div>
    @endif
@endif
