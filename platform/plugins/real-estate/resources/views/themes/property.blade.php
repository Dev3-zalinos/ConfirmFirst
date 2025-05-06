@php
    Theme::asset()
        ->usePath()
        ->add('leaflet-css', 'libraries/leaflet/leaflet.css');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('leaflet-js', 'libraries/leaflet/leaflet.js');
    Theme::asset()
        ->usePath()
        ->add('magnific-css', 'libraries/magnific/magnific-popup.css');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('magnific-js', 'libraries/magnific/jquery.magnific-popup.min.js');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('property-js', 'js/property.js');
@endphp
<main class="detailproject bg-white">
    <div data-property-id="{{ $property->id }}"></div>
    @include('plugins/real-estate::themes.includes.slider', ['object' => $property])

    <div class="container-fluid w90 padtop20">
        <h1 class="titlehouse">{{ $property->name }}</h1>
        @if (RealEstateHelper::isEnabledReview())
            <p style="margin-bottom: 5px;">@include('plugins/real-estate::themes.partials.review-star', [
                'avgStar' => $property->reviews_avg_star,
                'count' => $property->reviews_count,
            ])</p>
        @endif
        <p class="addresshouse">
            @if ($property->short_address)
                <span
                    class="d-inline-block"
                    style="margin-right: 10px"
                >
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $property->short_address }}
                </span>
            @endif
            @if (setting('real_estate_display_views_count_in_detail_page', 0) == 1)
                <span
                    class="d-inline-block"
                    style="margin-right: 10px"
                ><i class="fa fa-eye"></i> {{ number_format($property->views) }} {{ __('views') }}</span>
            @endif
            <span class="d-inline-block"><i class="fa fa-calendar-alt"></i>
                {{ $property->created_at->translatedFormat('M d, Y') }}</span>
        </p>
        <div class="d-none">
            {!! Theme::breadcrumb()->render() !!}
        </div>
        <p class="pricehouse"> {{ $property->price_html }} {!! $property->status_html !!}</p>
        <div class="row">
            <div class="col-md-8">
                {!! apply_filters('before_single_content_detail', null, $property) !!}

                <div class="row pt-3">
                    <div class="col-sm-12">
                        <h5 class="headifhouse">{{ __('Overview') }}</h5>
                        <div class="row py-2">
                            <div class="col-sm-12">
                                <table class="table table-striped table-bordered">
                                    @if ($lastUpdated = $property->getMetaData('last_updated', true))
                                        <tr>
                                            <td>{{ __('Last Updated') }}</td>
                                            <td>
                                                <strong>
                                                    {{ rescue(fn () => Carbon\Carbon::parse($lastUpdated)->translatedFormat('M d, Y')) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($property->unique_id)
                                        <tr>
                                            <td>{{ __('Property ID') }}</td>
                                            <td>
                                                <strong>
                                                    {{ $property->unique_id }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($property->categories->isNotEmpty())
                                        <tr>
                                            <td>{{ __('Category') }}</td>
                                            <td>
                                                <strong>
                                                    @foreach ($property->categories as $category)
                                                        <a href="{{ $category->url }}">{!! BaseHelper::clean($category->name) !!}</a>
                                                        @if (!$loop->last)
                                                            ,&nbsp;
                                                        @endif
                                                    @endforeach
                                                </strong>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($property->square)
                                        <tr>
                                            <td>{{ __('Square') }}</td>
                                            <td><strong>{{ $property->square_text }}</strong></td>
                                        </tr>
                                    @endif
                                    @if ($property->number_bedroom)
                                        <tr>
                                            <td>{{ __('Number of bedrooms') }}</td>
                                            <td><strong>{{ number_format($property->number_bedroom) }}</strong></td>
                                        </tr>
                                    @endif
                                    @if ($property->number_bathroom)
                                        <tr>
                                            <td>{{ __('Number of bathrooms') }}</td>
                                            <td><strong>{{ number_format($property->number_bathroom) }}</strong></td>
                                        </tr>
                                    @endif
                                    @if ($property->number_floor)
                                        <tr>
                                            <td>{{ __('Number of floors') }}</td>
                                            <td><strong>{{ number_format($property->number_floor) }}</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{ __('Price') }}</td>
                                        <td><strong>{{ $property->price_html }}</strong></td>
                                    </tr>
                                    @foreach ($property->customFields as $customField)
                                        <tr>
                                            <td>{!! BaseHelper::clean($customField->name) !!}</td>
                                            <td><strong>{!! BaseHelper::clean($customField->value) !!}</strong></td>
                                        </tr>
                                    @endforeach
                                    {!! apply_filters('property_details_extra_info', null, $property) !!}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($property->content)
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="headifhouse">{{ __('Description') }}</h5>
                            <div class="ck-content">
                                {!! BaseHelper::clean($property->content) !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if ($property->features->count())
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="headifhouse">{{ __('Features') }}</h5>
                            <div class="row">
                                @php $property->features->loadMissing('metadata'); @endphp
                                @foreach ($property->features as $feature)
                                    <div class="col-sm-4">
                                        @if ($feature->getMetaData('icon_image', true))
                                            <p><i><img
                                                        src="{{ RvMedia::getImageUrl($feature->getMetaData('icon_image', true)) }}"
                                                        alt="{{ $feature->name }}"
                                                        style="vertical-align: top; margin-top: 3px;"
                                                        width="18"
                                                        height="18"
                                                    ></i> {{ $feature->name }}</p>
                                        @else
                                            <p><i
                                                    class="@if ($feature->icon) {{ $feature->icon }} @else fas fa-check @endif text-orange text0i"></i>
                                                {{ $feature->name }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <br>
                @if ($property->facilities->isNotEmpty())
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="headifhouse">{{ __('Distance key between facilities') }}</h5>
                            <div class="row">
                                @php $property->facilities->loadMissing('metadata'); @endphp
                                @foreach ($property->facilities as $facility)
                                    <div class="col-sm-4">
                                        @if ($facility->getMetaData('icon_image', true))
                                            <p><i><img
                                                        src="{{ RvMedia::getImageUrl($facility->getMetaData('icon_image', true)) }}"
                                                        alt="{{ $facility->name }}"
                                                        style="vertical-align: top; margin-top: 3px;"
                                                        width="18"
                                                        height="18"
                                                    ></i> {{ $facility->name }} - {{ $facility->pivot->distance }}</p>
                                        @else
                                            <p><i
                                                    class="@if ($facility->icon) {{ $facility->icon }} @else fas fa-check @endif text-orange text0i"></i>
                                                {{ $facility->name }} - {{ $facility->pivot->distance }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <br>
                @endif
                @if (RealEstateHelper::isEnabledProjects() && $property->project_id && ($project = $property->project))
                    <div class="row pb-3">
                        <div class="col-sm-12">
                            <h5 class="headifhouse">{{ __("Project's information") }}</h5>
                        </div>
                        <div class="col-sm-12">
                            <div class="row item">
                                <div class="col-md-4 col-sm-5 pr-sm-0">
                                    <div class="img h-100 bg-light">
                                        <a href="{{ $project->url }}">
                                            <img
                                                class="thumb lazy"
                                                data-src="{{ RvMedia::getImageUrl($project->image, null, false, RvMedia::getDefaultImage()) }}"
                                                src="{{ RvMedia::getImageUrl($project->image, null, false, RvMedia::getDefaultImage()) }}"
                                                alt="{{ $project->name }}"
                                            >
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-8 col-sm-7 pt-2 pr-sm-0 bg-light">
                                    <h5><a
                                            class="font-weight-bold text-dark"
                                            href="{{ $project->url }}"
                                        >{!! BaseHelper::clean($project->name) !!}</a></h5>
                                    <div>{{ Str::limit($project->description, 120) }}</div>
                                    <p><a href="{{ $project->url }}">{{ __('Read more') }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <br>
                @if ($property->latitude && $property->longitude)
                    @include('plugins/real-estate::themes.partials.elements.traffic-map-modal', ['location' => $property->location])

                    <div class="d-none d-print-block">
                        <a
                            class="text-decoration-none"
                            href="https://maps.google.com/?ll={{ $property->latitude }},{{ $property->longitude }}"
                        >
                            {{ $property->location ?: $address }}
                        </a>
                    </div>
                @else
                    @include('plugins/real-estate::themes.partials.elements.gmap-canvas', ['location' => $property->location])

                    <div class="d-none d-print-block">
                        <a
                            class="text-decoration-none"
                            href="https://www.google.com/maps/search/{{ urlencode($property->location) }}"
                        >
                            {{ $property->location ?: $address }}
                        </a>
                    </div>
                @endif
                <br>
                @if ($property->video_url)
                    @include('plugins/real-estate::themes.partials.elements.video', ['object' => $property, 'title' => __('Property video')])
                @endif

                {!! apply_filters('after_single_content_detail', null, $property) !!}

                <br>
{{--                {!! Theme::partial('share', ['title' => __('Share this property'), 'description' => $property->description]) !!}--}}
                <div class="clearfix"></div>
                {!! apply_filters(
                    BASE_FILTER_PUBLIC_COMMENT_AREA,
                    theme_option('facebook_comment_enabled_in_property', 'no') == 'yes' ? Theme::partial('comments') : null,
                ) !!}

                {!! apply_filters('after_property_detail_content', null, $property) !!}

                <br>
                @if (RealEstateHelper::isEnabledReview())
                    @include('plugins/real-estate::themes.partials.reviews', [
                        'model' => $property,
                    ])
                @endif
            </div>
            <div class="col-md-4">
                {!! apply_filters('property_right_details_info', null, $property) !!}

                @if (!RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
                    <div class="boxright p-3">
                        <div class="head">
                            {{ __('Contact agency') }}
                        </div>

                        <div class="row rowm10 itemagent">
                            <div class="col-lg-4 colm10">
                                @if ($account->url)
                                    <a href="{{ $account->url }}">
                                        @if ($account->avatar->url)
                                            <img
                                                class="img-thumbnail"
                                                src="{{ RvMedia::getImageUrl($account->avatar->url, 'thumb') }}"
                                                alt="{{ $account->name }}"
                                            >
                                        @else
                                            <img
                                                class="img-thumbnail"
                                                src="{{ $account->avatar_url }}"
                                                alt="{{ $account->name }}"
                                            >
                                        @endif
                                    </a>
                                @else
                                    @if ($account->avatar->url)
                                        <img
                                            class="img-thumbnail"
                                            src="{{ RvMedia::getImageUrl($account->avatar->url, 'thumb') }}"
                                            alt="{{ $account->name }}"
                                        >
                                    @else
                                        <img
                                            class="img-thumbnail"
                                            src="{{ $account->avatar_url }}"
                                            alt="{{ $account->name }}"
                                        >
                                    @endif
                                @endif
                            </div>
                            <div class="col-lg-8 colm10">
                                <div class="info">
                                    <p>
                                        <strong>
                                            @if ($account->url)
                                                <a href="{{ $account->url }}">{{ $account->name }}</a>
                                            @else
                                                {{ $account->name }}
                                            @endif
                                        </strong>
                                    </p>
                                    @if ($account->phone && !setting('real_estate_hide_agency_phone', 0))
                                        @php
                                            Theme::set('hotlineNumber', $account->phone);
                                        @endphp
                                        <p
                                            class="mobile"
                                            dir="ltr"
                                        ><a href="tel:{{ $account->phone }}">{{ $account->phone }}</a></p>
                                    @elseif ($hotline = theme_option('hotline'))
                                        <p
                                            class="mobile"
                                            dir="ltr"
                                        ><a href="tel:{{ $hotline }}">{{ $hotline }}</a>
                                        </p>
                                    @endif
                                    @if ($account->email && !setting('real_estate_hide_agency_email', 0))
                                        <p><a href="mailto:{{ $account->email }}">{{ $account->email }}</a></p>
                                    @endif
                                    @if ($account->url)
                                        <p><span class="fas fa-arrow-circle-right me-1"></span> <a
                                                href="{{ $account->url }}"
                                            >{{ __('More properties by this agent') }}</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="boxright p-3">
{{--                    {!! Theme::partial('consult-form', ['type' => 'property', 'data' => $property]) !!}--}}
                </div>
            </div>
        </div>
        <br>
        <div class="projecthome mb-2">
            @php
                $relatedProperties = app(\Botble\RealEstate\Repositories\Interfaces\PropertyInterface::class)
                    ->getRelatedProperties(
                        $property->id,
                        theme_option('number_of_related_properties', 8),
                        \Botble\RealEstate\Facades\RealEstateHelper::getPropertyRelationsQuery()
                    );
            @endphp

            @if ($relatedProperties->isNotEmpty())
                <h3 class="headifhouse">{{ __('Related properties') }}</h3>
                <div class="row rowm10">
                    @foreach ($relatedProperties as $relatedProperty)
                        <div class="col-sm-6 col-lg-4 col-xl-3 colm10">
                            @include('plugins/real-estate::themes.partials.properties.item', ['property' => $relatedProperty])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>

<script id="traffic-popup-map-template" type="text/x-custom-template">
    @include('plugins/real-estate::themes.partials.properties.map', ['property' => $property])
</script>
