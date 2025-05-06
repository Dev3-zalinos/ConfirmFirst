@php
    SeoHelper::setTitle(__('404 - Page Not found'));
    Theme::fireEventGlobalAssets();
@endphp

@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    <div class="error-page">
        <h2 class="error-header">404</h2>
        <p class="error-title">{{ __('Oops… You just found an error page!') }}</p>
        <a href="{{ BaseHelper::getHomepageUrl() }}" class="tf-btn primary">{{ __('Back To Home') }}</a>
    </div>
@endsection
