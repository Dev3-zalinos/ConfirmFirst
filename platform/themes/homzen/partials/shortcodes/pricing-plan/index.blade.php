<section class="flat-section flat-pricing">
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        <div class="row">
            @foreach ($packages as $package)
                <div class="box col-lg-{{ max(round(12 / $packages->count()), 3) }} col-md-6 g-4">
                    <div @class(['box-pricing', 'active' => $package->is_default])>
                        <div class="price d-flex align-items-end">
                            <h4>{{ $package->price == 0 ? __('Free') : format_price($package->price) }}</h4>
                            <span class="body-2 text-variant-1">
                                /
                                @if ($package->number_of_listings === 1)
                                    {{ __('1 post') }}
                                @else
                                    {{ __(':number posts', ['number' => number_format($package->number_of_listings)]) }}
                                @endif
                            </span>
                        </div>
                        <div class="box-title-price">
                            <h6 class="title">{!! BaseHelper::clean($package->name) !!}</h6>
                            @if ($package->description)
                                <p class="desc">{{ $package->description }}</p>
                            @endif
                        </div>
                        @if ($package->formatted_features)
                            <ul class="list-price">
                                @foreach ($package->formatted_features as $feature)
                                    <li class="item">
                                        <span class="check-icon icon-tick"></span>
                                        {!! BaseHelper::clean($feature) !!}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a
                            href="{{ route('public.account.packages') }}"
                            class="tf-btn"
                        >
                            {{ __('Choose The Package') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
