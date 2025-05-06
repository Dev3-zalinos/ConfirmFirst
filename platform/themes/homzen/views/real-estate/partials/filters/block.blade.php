@if (theme_option('real_estate_enable_filter_by_block', 'yes') == 'yes')
    <div @class(['box-select form-search-block',  $class ?? null])>
    <label for="block" class="title-select fw-5">{{ __('Blocks') }}</label>
        <select name="block" id="block" class="select_js">
            <option value="">{{ __('All') }}</option>
            @foreach(range(1, 5) as $i)
                <option value="{{ $i }}">
                    @if($i < 5)
                        {{ $i === 1 ? __('1 Block') : __(':number Blocks', ['number' => $i]) }}
                    @else
                        {{ __(':number+ Blocks', ['number' => $i]) }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
