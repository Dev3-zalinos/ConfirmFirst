@php
    use Botble\Shortcode\Facades\Shortcode;

    $contactInfo = Shortcode::fields()->getTabsData(['label', 'content'], $shortcode);
@endphp

<section class="flat-section flat-contact">
    <div class="container">
        @if($shortcode->show_information_box)
        <div class="row">
            <div class="col-lg-8">
        @endif
                <div class="contact-content">
                    @if($shortcode->title)
                        <h5>{!! BaseHelper::clean($shortcode->title) !!}</h5>
                    @endif
                    @if($shortcode->description)
                        <p class="body-2 text-variant-1">{!! BaseHelper::clean($shortcode->description) !!}</p>
                    @endif

                    {!! $form->renderForm() !!}
                </div>
        @if($shortcode->show_information_box)
            </div>
            <div class="col-lg-4">
                <div class="contact-info">
                    <h5>{!! BaseHelper::clean($shortcode->contact_title) !!}</h5>
                    <ul class="contact-form-list">
                        @foreach($contactInfo as $item)
                            <li class="box">
                                <div class="text-1 title">{!! BaseHelper::clean($item['label']) !!}</div>
                                <p class="p-16 text-variant-1">{!! BaseHelper::clean(nl2br($item['content'])) !!}</p>
                            </li>
                        @endforeach

                        @if($shortcode->show_social_links && ($items = Theme::getSocialLinks()))
                            <li class="box">
                                <div class="text-1 title">{{ __('Follow Us:') }}</div>
                                <ul class="box-social">
                                    @foreach($items as $item)
                                        <li>
                                            <a title="{{ $item->getName() }}" href="{{ $item->getUrl() }}" class="item">
                                                {!! $item->getIconHtml(['style' => 'stroke-width: 2']) !!}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
        </div>
    </div>
</section>
