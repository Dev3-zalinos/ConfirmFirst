 <div @class(['widget-box single-property-contact', $class ?? null])>
    <div class="h7 title fw-7">{{ __('Contact Agency') }}</div>
    @if (! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
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

    {!! apply_filters('property_right_details_info', null, $property) !!}

    {!! apply_filters('before_consult_form', null, $property) !!}

    {!! \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
        ->formClass('contact-form')
        ->setFormInputWrapperClass('ip-group')
        ->modify('content', 'textarea', ['attr' => ['class' => 'form-control']])
        ->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])
        ->add('type', 'hidden', ['attr' => ['value' => 'property']])
        ->add('data_id', 'hidden', ['attr' => ['value' => $property->getKey()]])
        ->addBefore('content', 'data_name', 'text', ['label' => false, 'attr' => ['value' => $property->name, 'disabled' => true]])
        ->renderForm()
    !!}

    {!! apply_filters('after_consult_form', null, $property) !!}
</div>

