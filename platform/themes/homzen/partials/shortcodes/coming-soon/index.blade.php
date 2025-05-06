<section class="flat-section container coming-soon-box" style="display: flex; height: 100vh">
    @if($shortcode->image)
        <div class="row align-items-center">
            <div class="col-lg-5 mb-30">
        @endif
                @if ($countdownTime)
                    <div class="coming-soon-countdown mb-4" data-countdown data-date="{{ $countdownTime }}">
                        <ul class="coming-soon-countdown-inner">
                            <li><span data-days>0</span> <span class="label">{{ __('Days') }}</span></li>
                            <li><span data-hours>0</span> <span class="label">{{ __('Hours') }}</span></li>
                            <li><span data-minutes>0</span> <span class="label">{{ __('Minutes') }}</span></li>
                            <li><span data-seconds>0</span> <span class="label">{{ __('Seconds') }}</span></li>
                        </ul>
                    </div>
                @endif

                @if($shortcode->title)
                    <h2 class="section-title mt-4">
                        {!! BaseHelper::clean($shortcode->title) !!}
                    </h2>
                @endif

                @if ($form)
                    {!! $form->renderForm() !!}
                @endif

                <div class="mt-30 footer-info">
                    <ul class="mt-12">
                        @if ($address = $shortcode->address)
                            <li class="mt-12 d-flex align-items-center gap-8">
                                <p><x-core::icon name="ti ti-map-pin" /> {!! BaseHelper::clean($address) !!}</p>
                            </li>
                        @endif

                        @if ($hotline = $shortcode->hotline)
                            <li class="mt-12 d-flex align-items-center gap-8">
                                <p>
                                    <x-core::icon name="ti ti-phone" /> <a href="tel:{{ $hotline }}" dir="ltr">{{ $hotline }}</a>
                                </p>
                            </li>
                        @endif

                        @if ($businessHours = $shortcode->business_hours)
                            <li class="mt-12 d-flex align-items-center gap-8">
                                <p><x-core::icon name="ti ti-clock" /> {!! BaseHelper::clean(nl2br($businessHours)) !!}</p>
                            </li>
                        @endif
                    </ul>
                </div>

                @if($shortcode->show_social_links ?? true)
                    @if($socialLinks = Theme::getSocialLinks())
                        <ul class="d-flex flex-wrap gap-12 mt-3">
                            @foreach($socialLinks as $socialLink)
                                @continue(! $socialLink->getUrl() || ! $socialLink->getIconHtml())

                                <a {!! $socialLink->getAttributes() !!} class="box-icon w-40 social square">{{ $socialLink->getIconHtml() }}</a>
                            @endforeach
                        </ul>
                    @endif
                @endif

                @if($shortcode->image)
            </div>
            <div class="col-lg-7 mb-30">
                {{ RvMedia::image($shortcode->image, $shortcode->title, attributes: ['class' => 'coming-soon-image']) }}
            </div>
            @endif
        </div>
</section>
