@if (theme_option('real_estate_enable_filter_by_bedroom', 'yes') == 'yes')
    <div @class(['box-select form-search-bedroom',  $class ?? null])>
    <label for="bedroom" class="title-select text-variant-1">{{ __('Bedrooms') }}</label>
        <select name="bedroom" id="bedroom" class="select_js">
            <option value="">{{ __('All') }}</option>
            <option value="Studio">{{ __('Studio') }}</option>
            @foreach(range(1, 5) as $i)
                <option value="{{ $i }}">
                    @if($i < 5)
                        {{ $i === 1 ? __('1 Bedroom') : __(':number Bedrooms', ['number' => $i]) }}
                    @else
                        {{ __(':number+ Bedrooms', ['number' => $i]) }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
