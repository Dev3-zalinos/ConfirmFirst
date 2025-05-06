<div class="content-bottom">
    <div class="info-box">
        <ul class="meta">
            @if (RealEstateHelper::isEnabledReview())
                <li class="meta-item">
                    <div class="rating-star" style="--bb-rating-size: 100px">
                        <span style="width: {{ $model->reviews_avg_star * 20 }}%;"></span>
                    </div>
                    ({{ $model->reviews_count ?: 0 }})
                </li>
            @endif

            @if (setting('real_estate_display_views_count_in_detail_page', true))
                <li class="meta-item">
                    <x-core::icon name="ti ti-eye" />
                    @if ($model->views === 1)
                        {{ __('1 Views') }}
                    @else
                        {{ __(':number Views', ['number' => number_format($model->views)]) }}
                    @endif
                </li>
            @endif

            @if (theme_option('real_estate_show_listing_date_on_single_detail_page', 'yes') == 'yes')
                <li class="meta-item">
                    <x-core::icon name="ti ti-calendar" />
                    {{ Theme::formatDate($model->created_at) }}
                </li>
            @endif

            @if ($model->short_address)
                <p class="meta-item">
                    <x-core::icon name="ti ti-map-pin" />
                    {{ $model->short_address }}
                </p>
            @endif
        </ul>
    </div>
    @if (RealEstateHelper::isEnabledWishlist())
        <ul class="icon-box">
            <li>
                <button type="button" class="item" data-type="{{ $model instanceof \Botble\RealEstate\Models\Property ? 'property' : 'project' }}"
                        data-bb-toggle="add-to-wishlist"
                        data-id="{{ $model->getKey() }}"
                        data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $model->name]) }}"
                        data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $model->name]) }}"
                >
                    <x-core::icon name="ti ti-heart" />
                </button>
            </li>
        </ul>
    @endif
</div>
