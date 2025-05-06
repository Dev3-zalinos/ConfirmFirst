@php
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.css');
    Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');
    Theme::layout('full-width');
    Theme::set('pageTitle', $project->name);
@endphp

@include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.gallery-slider'), ['model' => $project])

<section class="flat-section pt-0 flat-property-detail">
    <div class="container">
        <div class="header-property-detail">
            <div class="content-top d-flex justify-content-between align-items-center">
                <div class="box-name">
                    {!! BaseHelper::clean($project->status_html) !!}
                    <h4 class="title link">
                        {!! BaseHelper::clean($project->name) !!}
                    </h4>
                </div>

                @if ($project->price_from || $project->price_to)
                    <div class="box-price d-flex align-items-center">
                        <h4>{{ $project->formatted_price }}</h4>
                    </div>
                @endif
            </div>
            @include(Theme::getThemeNamespace('views.real-estate.partials.meta'), ['model' => $project])
        </div>
        <div class="row">
            <div class="col-lg-8">
                {!! apply_filters('before_single_content_detail', null, $project) !!}

                @if ($project->content)
                    <div class="single-property-element single-property-desc">
                        <div class="h7 title fw-7">{{ __('Description') }}</div>
                        <div class="body-2 text-variant-1">
                            <div class="ck-content single-detail">
                                {!! BaseHelper::clean($project->content) !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if ($videoUrl = $project->getMetaData('video_url', true))
                    <div class="single-property-element single-property-video">
                        <div class="h7 title fw-7">{{ __('Video') }}</div>
                        <div class="img-video">
                            <img src="{{ RvMedia::getImageUrl($project->getMetaData('video_thumbnail', true)) ?: \Botble\Theme\Supports\Youtube::getThumbnail($videoUrl) }}" alt="{{ $project->name }}">
                            <a href="{{ $videoUrl }}" @if(\Botble\Theme\Supports\Youtube::isYoutubeURL($videoUrl)) data-fancybox="gallery2" @endif class="btn-video">
                                <x-core::icon name="ti ti-player-play-filled" />
                            </a>
                        </div>
                    </div>
                @endif
                <div class="single-property-element single-property-overview">
                    <div class="h7 title fw-7">{{ __('Overview') }}</div>
                    <div class="row row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4 info-box">
                        <div class="col item">
                            <div class="box-icon w-52">
                                <x-core::icon name="ti ti-home" />
                            </div>
                            <div class="content">
                                <span class="label">{{ __('Project ID:') }}</span>
                                <span>{{ $project->unique_id ?: $project->getKey() }}</span>
                            </div>
                        </div>
                        @if($project->categories->isNotEmpty())
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-category" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Type:') }}</span>
                                    <span>
                                        {{ $project->categories->map(fn ($item) => $item->name)->implode(', ') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if ($project->investor->name)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-category" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Investor:') }}</span>
                                    <span>{{ $project->investor->name }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->number_block)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-packages" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Blocks:') }}</span>
                                    <span>{{ number_format($project->number_block) }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->number_floor)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-stairs" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Floors:') }}</span>
                                    <span>{{ number_format($project->number_floor) }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->number_flat)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-building" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Flats:') }}</span>
                                    <span>{{ number_format($project->number_flat) }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->square)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-ruler-2" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Square:') }}</span>
                                    <span>{{ $project->square_text }}</span>
                                </div>
                            </div>
                        @endif
                        @foreach ($project->customFields as $customField)
                            @continue(! $customField->value)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-box" />
                                </div>
                                <div class="content">
                                    <span class="label">{!! BaseHelper::clean($customField->name) !!}:</span>
                                    <span>{!! BaseHelper::clean($customField->value) !!}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if ($project->features->isNotEmpty())
                    <div class="single-property-element single-property-feature">
                        <div class="h7 title fw-7">{{ __('Amenities and features') }}</div>
                        <div class="box-feature">
                            <ul>
                                @foreach ($project->features as $feature)
                                    <li class="feature-item">
                                        @if ($feature->icon)
                                            {!! BaseHelper::renderIcon($feature->icon) !!}
                                        @endif
                                        {{ $feature->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if ($project->facilities->isNotEmpty())
                    <div class="single-property-element single-property-nearby">
                        <div class="h7 title fw-7">{{ __('What’s nearby?') }}</div>
                        <p class="body-2">{{ __("Explore nearby amenities to precisely locate your property and identify surrounding conveniences, providing a comprehensive overview of the living environment and the property's convenience.") }}</p>
                        <ul class="grid-3 box-nearby">
                            @foreach ($project->facilities as $facility)
                                <li class="item-nearby">
                                    <span class="label">
                                        @if($facility->icon)
                                            {!! BaseHelper::renderIcon($facility->icon) !!}
                                        @endif
                                        {{ $facility->name }}:
                                    </span>
                                    <span class="fw-7">{{ $facility->pivot->distance }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="single-property-element single-property-map">
                    <div class="h7 title fw-7">{{ __('Location') }}</div>
                    @if (theme_option('real_estate_show_map_on_single_detail_page', 'yes') === 'yes')
                        @if ($project->latitude && $project->longitude)
                            <div data-bb-toggle="detail-map" id="map" style="min-height: 400px;" data-tile-layer="{{ RealEstateHelper::getMapTileLayer() }}" data-center="{{ json_encode([$project->latitude, $project->longitude]) }}" data-map-icon="{{ $project->map_icon }}"></div>
                        @else
                            <iframe width="100%" style="min-height: 400px" src="https://maps.google.com/maps?q={{ urlencode($project->location) }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        @endif
                    @endif

                    @if ($locationOnMap = ($project->location ?: $project->short_address))
                        @php
                            $mapUrl = 'https://www.google.com/maps/search/' . urlencode($locationOnMap);

                            if ($project->latitude && $project->longitude) {
                                $mapUrl = 'https://maps.google.com/?q=' . $project->latitude . ',' . $project->longitude;
                            }
                        @endphp
                        <ul class="info-map">
                            <li>
                                <div class="fw-7">{{ __('Address') }}</div>
                                <a class="mt-4 text-variant-1" href="{{ $mapUrl }}" target="_blank">
                                    {{ $locationOnMap }}
                                </a>
                            </li>
                        </ul>
                    @endif

                    @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), ['model' => $project])
                </div>

                {!! apply_filters('after_single_content_detail', null, $project) !!}

                {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $project) !!}

                @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.reviews'), ['model' => $project])
            </div>
            <div class="col-lg-4">
                <div class="widget-sidebar wrapper-sidebar-right">
                        <div class="widget-box single-property-contact bg-surface">
                            <div class="h7 title fw-7">{{ __('Contact Agency') }}</div>

                            @if (! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $project->author))
                                <div class="box-avatar">
                                    <div class="avatar avt-100 round">
                                        <a href="{{ $account->url }}" class="d-block">
                                            {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                                        </a>
                                    </div>
                                    <div class="info line-clamp-1">
                                        <div class="text-1 name">
                                            <a href="{{ $account->url }}">{{ $account->name }}</a>
                                        </div>
                                        @if ($account->phone && ! setting('real_estate_hide_agency_phone', false))
                                            <a href="tel:{{ $account->phone }}" class="info-item">{{ $account->phone }}</a>
                                        @elseif($hotline = theme_option('hotline'))
                                            <a href="tel:{{ $hotline }}" class="info-item">{{ $hotline }}</a>
                                        @endif
                                        @if ($account->email && ! setting('real_estate_hide_agency_email', false))
                                            <a href="mailto:{{ $account->email }}" class="info-item">{{ $account->email }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {!! apply_filters('project_right_details_info', null, $project) !!}

                            {!! apply_filters('before_consult_form', null, $project) !!}

                            {!! \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
                                ->formClass('contact-form')
                                ->setFormInputWrapperClass('ip-group')
                                ->modify('content', 'textarea', ['attr' => ['class' => '']])
                                ->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])
                                ->add('type', 'hidden', ['attr' => ['value' => 'project']])
                                ->add('data_id', 'hidden', ['attr' => ['value' => $project->getKey()]])
                                ->addBefore('content', 'data_name', 'text', ['label' => false, 'attr' => ['value' => $project->name, 'disabled' => true]])
                                ->renderForm()
                            !!}

                            {!! apply_filters('after_consult_form', null, $project) !!}
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $relatedProperties = app(\Botble\RealEstate\Repositories\Interfaces\PropertyInterface::class)
    ->getPropertiesByConditions(
        [
            're_properties.project_id' => $project->getKey(),
        ],
        8,
        \Botble\RealEstate\Facades\RealEstateHelper::getPropertyRelationsQuery(),
    );
@endphp

@if ($relatedProperties->isNotEmpty())
    <section class="flat-section pt-0 flat-latest-property">
        <div class="container">
            <div class="box-title">
                <div class="text-subtitle text-primary">{{ __('Latest Properties') }}</div>
                <h2 class="section-title mt-4">{{ __('Properties in project ":name"', ['name' => $project->name]) }}</h2>
            </div>
            <div class="swiper tf-latest-property" data-preview-lg="3" data-preview-md="2" data-preview-sm="2" data-space="30" data-loop="true">
                <div class="swiper-wrapper">
                    @foreach($relatedProperties as $property)
                        <div class="swiper-slide">
                            @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'), ['property' => $property, 'class' => 'style-2'])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

<template id="map-popup-content">
    <div class="map-popup-content">
        <a class="map-popup-content-thumb" href="{{ $project->url }}">
            {{ RvMedia::image($project->image_thumb, $project->name, lazy: false) }}
            {!! BaseHelper::clean($project->status_html) !!}
        </a>
        <div class="map-popup-content__details">
            <h5 class="map-popup-content__title">
                <a href="{{ $project->url }}" target="_blank" class="map-popup-content__link">{{ $project->name }}</a>
            </h5>
            <div class="map-popup-content__price">{{ $project->category_name }}</div>
            <div class="map-popup-content__city">
                <x-core::icon name="ti ti-map-pin" />
                {{ $project->short_address }}
            </div>
        </div>
    </div>
</template>
