@php
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
@endphp

<section
    class="flat-section-v3 flat-slider-contact"
    @style(["background-image: url('$backgroundImage') !important" => $backgroundImage])
>
    <div class="container">
        <div class="row content-wrap">
            <div class="col-lg-7">
                <div class="content-left">
                    <div class="box-title">
                        @if($shortcode->title)
                            <h2 class="section-title mt-4 fw-6 text-white">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                        @endif
                        @if($shortcode->subtitle)
                            <div class="text-subtitle text-white">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                        @endif
                    </div>
                    @if($shortcode->description)
                        <p class="body-body-2 text-white">{!! BaseHelper::clean($shortcode->description) !!}</p>
                    @endif
                </div>
            </div>
            <div class="col-lg-5">
                <div class="box-contact-v2">
                    {!! $form->renderForm() !!}
                </div>
            </div>
        </div>

    </div>
    <div class="overlay"></div>
</section>
