@if ($logo = apply_filters('real_estate_dashboard_logo', Theme::getLogo()))
    <a @if (isset($cssClass)) class="{{ $cssClass }}" @endif href="{{ route('public.index') }}" title="{{ $siteTitle = Theme::getSiteTitle() }}">
        <img
            src="{{ RvMedia::getImageUrl($logo) }}"
            alt="{{ $siteTitle }}"
            style="max-height: {{ (theme_option('logo_height', 40) ?: 40) }}px"
        >
    </a>
@endif
