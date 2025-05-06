@php
    Theme::set('pageTitle', $category->name);
@endphp

<div class="flat-section">
    <section class="flat-recommended">
        @include(Theme::getThemeNamespace('views.real-estate.properties.index'))
    </section>
</div>
