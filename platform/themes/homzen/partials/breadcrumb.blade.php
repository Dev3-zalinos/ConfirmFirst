@php
    $backgroundColor = Theme::get('breadcrumbBackgroundColor', theme_option('breadcrumb_background_color', '#f7f7f7'));
    $textColor = Theme::get('breadcrumbTextColor', theme_option('breadcrumb_text_color', '#161e2d'));
    $backgroundImage = Theme::get('breadcrumbBackgroundImage', theme_option('breadcrumb_background_image') ?: null);

    $backgroundImage = $backgroundImage ? RvMedia::getImageUrl($backgroundImage) : null;

    $showBreadcrumb = Theme::get('breadcrumbEnabled', 'yes');
@endphp

@if ($showBreadcrumb === 'yes')
    <section class="flat-title-page style-2" @style(["background-color: $backgroundColor" => $backgroundColor != 'transparent', "background-image: url($backgroundImage); background-size: cover; background-position: center" => $backgroundImage])>
        <div class="container">
            <ul class="breadcrumb">
                @foreach(Theme::breadcrumb()->getCrumbs() as $crumb)
                    <li>
                        @if($loop->last)
                            {!! BaseHelper::clean($crumb['label']) !!}
                        @else
                            <a href="{{ $crumb['url'] }}" @style(["color: $textColor" => $textColor != 'transparent'])>{!! BaseHelper::clean($crumb['label']) !!}</a>
                            <span class="ms-1" @style(["color: $textColor" => $textColor != 'transparent'])>/</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            <h1 class="text-center page-title" @style(["color: $textColor" => $textColor && $textColor != 'transparent'])>{!! BaseHelper::clean(Theme::get('pageTitle') ? Theme::get('pageTitle') : SeoHelper::getTitleOnly()) !!}</h1>
        </div>
    </section>
@endif
