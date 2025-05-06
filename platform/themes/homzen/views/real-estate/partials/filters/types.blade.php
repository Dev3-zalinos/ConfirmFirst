@if($enabledTypes = Botble\RealEstate\Facades\RealEstateHelper::enabledPropertyTypes())
    <div @class(['box-select form-search-type',  $class ?? null])>
        <label for="floor" class="title-select fw-5">{{ __('Type') }}</label>
        <select name="type" id="select-type" class="select_js">
            <option value="">{{ __('-- Select --') }}</option>
            @foreach(Botble\RealEstate\Enums\PropertyTypeEnum::labels() as $key => $label)
                @continue(! in_array($key, $enabledTypes))

                <option value="{{ $key }}" @selected(request()->input('type') == $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
@endif
