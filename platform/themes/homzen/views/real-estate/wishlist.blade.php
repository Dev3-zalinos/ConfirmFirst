@php
    Theme::set('pageTitle', __('Wishlist'));
@endphp

<div class="flat-section">
    @if($properties->isNotEmpty() || $projects->isNotEmpty())
        <section class="flat-recommended">
            @if($properties->isNotEmpty())
                <div class="box-title text-center">
                    <h2 class="section-title mt-4">{{ __('Your Favorite Properties') }}</h2>
                </div>

                @include(Theme::getThemeNamespace('views.real-estate.properties.index'))
            @endif
        </section>

        @if($projects->isNotEmpty())
            <section class="flat-section pb-0 flat-recommended">
                <div class="box-title text-center">
                    <h2 class="section-title mt-4">{{ __('Your Favorite Projects') }}</h2>
                </div>

                @include(Theme::getThemeNamespace('views.real-estate.projects.index'))
            </section>
        @endif
    @else
        <p class="text-center body-2">{{ __('You have not added any properties or projects to your wishlist.') }}</p>
    @endif
</div>
