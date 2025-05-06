@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    {!! apply_filters('ads_render', null, 'header_before') !!}

    {!! apply_filters('theme_front_header_content', null) !!}

    {!! Theme::partial('top-header') !!}

    {!! Theme::partial('header') !!}

    {!! apply_filters('ads_render', null, 'header_after') !!}

    @if(Theme::get('breadcrumbEnabled', 'yes') === 'yes')
        {!! Theme::breadcrumb()->render(Theme::getThemeNamespace('partials.breadcrumb')) !!}
    @endif

    <div class="container">
        {!! Theme::content() !!}
    </div>

    {!! apply_filters('ads_render', null, 'footer_before') !!}

    {!! apply_filters('theme_front_footer_content', null) !!}

    {!! Theme::partial('footer') !!}

    {!! apply_filters('ads_render', null, 'footer_after') !!}
@endsection
