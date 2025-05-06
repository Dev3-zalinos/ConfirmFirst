@if (theme_option('real_estate_enable_filter_by_bathroom', 'yes') == 'yes')
    <div @class(['box-select form-search-bathroom',  $class ?? null])>
        <label for="bathroom" class="title-select text-variant-1">{{ __('Bathrooms') }}</label>
        <select name="bathroom" id="bathroom" class="select_js">
            <option value="">{{ __('All') }}</option>
            @foreach(range(1, 5) as $i)
                <option value="{{ $i }}">
                    @if($i < 5)
                        {{ $i === 1 ? __('1 Bathroom') : __(':number Bathrooms', ['number' => $i]) }}
                    @else
                        {{ __(':number+ Bathrooms', ['number' => $i]) }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
