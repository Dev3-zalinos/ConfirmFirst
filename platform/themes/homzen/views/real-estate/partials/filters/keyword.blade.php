<div class="form-group-1 form-search-form form-style form-search-keyword-input" data-bb-toggle="search-suggestion">
    <label>{{ __('Keyword') }}</label>
    <div class="position-relative">
        <input type="text" class="form-control" placeholder="{{ __('Search for Keyword') }}" value="{{ BaseHelper::stringify(request()->query('k')) }}" name="k" />
        <div data-bb-toggle="data-suggestion"></div>
    </div>
</div>
