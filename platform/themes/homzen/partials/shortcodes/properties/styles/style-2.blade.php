<section
    class="flat-section flat-recommended"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'hasButton' => false]) !!}

        <div class="position-relative flat-tab-recommended wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
            <ul
                @class(['nav-tab-recommended justify-content-center', 'd-none' => count($tabs) < 2])
                role="tablist"
                data-bb-toggle="properties-tab"
                data-url="{{ route('public.ajax.properties') }}"
                data-attributes="{{ json_encode([
                    'type' => $shortcode->type,
                    'is_featured' => $shortcode->is_featured ? 1 : 0,
                    'limit' => $shortcode->limit,
                    'category_ids' => $categoryIds,
                ]) }}"
            >
                <li class="nav-tab-item" role="presentation">
                    <a href="#all" class="nav-link-item active" data-bs-toggle="tab">
                        {{ __('View All') }}
                    </a>
                </li>
                @foreach($tabs as $key => $tab)
                    <li class="nav-tab-item" role="presentation">
                        <a href="#{{ Str::slug($tab) }}" data-bb-value="{{ $key }}" class="nav-link-item" data-bs-toggle="tab">
                            {{ $tab }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active show" role="tabpanel">
                    <div data-bb-toggle="properties-tab-slot">
                        @if ($properties->isNotEmpty())
                            @include(Theme::getThemeNamespace('views.real-estate.properties.index'), ['properties' => $properties, 'itemsPerRow' => 3])
                        @endif
                    </div>

                    @if($shortcode->button_url && $shortcode->button_label)
                        <div class="text-center">
                            <a href="{{ $shortcode->button_url }}" class="tf-btn primary size-1">
                                {!! BaseHelper::clean($shortcode->button_label) !!}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
