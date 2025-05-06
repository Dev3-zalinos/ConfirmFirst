@if (theme_option('real_estate_enable_filter_by_square', 'yes') == 'yes')
    @php
        $minSquare = get_min_square() ?: 0;
        $maxSquare = get_max_square() ?: 10000;
    @endphp

    @if ($minSquare < $maxSquare)
        <div @class(['widget-price form-search-square', $class ?? null])>
            <div class="box-title-price">
                <span class="title-price">{{ __('Square Range') }}</span>
                <div class="caption-price">
                    <span>{{ __('from') }}</span>
                    <span id="slider-range-value01" class="fw-7 ms-1 me-1"></span>
                    <span>{{ __('to') }}</span>
                    <span id="slider-range-value02" class="fw-7 ms-1"></span>
                </div>
            </div>
            <div
                id="slider-range2"
                data-min="{{ get_min_square() ?: 0 }}"
                data-max="{{ get_max_square() ?: 10000 }}"
                data-unit="{{ setting('real_estate_square_unit', 'm²') }}"
            ></div>
            <div class="slider-labels">
                <div>
                    <input type="hidden" name="min_square" value="{{ BaseHelper::stringify(request()->query('min_square')) }}" />
                    <input type="hidden" name="max_square" value="{{ BaseHelper::stringify(request()->query('max_square')) }}" />
                </div>
            </div>
        </div>
    @endif
@endif
