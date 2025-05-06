@php
    $symbol = '';
    $currency = get_application_currency();
    if ($currency) {
        $symbol = ' (' . $currency->symbol . ')';
    }
@endphp

<div class="col-sm-3 px-md-1">
    <label for="min_price" class="control-label">{{ __('Price from') . $symbol }}</label>
    <input type="number" name="min_price" class="form-control min-input" id="min_price"
           value="{{ BaseHelper::stringify(request()->input('min_price')) }}" placeholder="{{ __('From') }}" min="0" step="1">
</div>
<div class="col-sm-3 px-md-1">
    <label for="max_price" class="control-label">{{ __('Price to') . $symbol }}</label>
    <input type="number" name="max_price" class="form-control max-input" id="max_price"
           value="{{ BaseHelper::stringify(request()->input('max_price')) }}" placeholder="{{ __('To') }}" min="0" step="1">
</div>
