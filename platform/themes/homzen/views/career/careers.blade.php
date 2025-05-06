<section class="flat-section">
    @include(Theme::getThemeNamespace('views.career.partials.list'))

    @if($careers->hasPages())
        <div class="d-flex align-items-center justify-content-center mt-3">
            {{ $careers->links(Theme::getThemeNamespace('partials.pagination')) }}
        </div>
    @endif
</section>
