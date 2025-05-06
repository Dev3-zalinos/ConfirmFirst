<section class="flat-section">
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode]) !!}

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @switch($shortcode->display_type)
                    @case('list')
                        <div class="tf-faq">
                            <ul class="box-faq" id="wrapper-faq">
                                @foreach($faqs as $faq)
                                    <li class="faq-item">
                                        <a href="#accordion-faq-{{ $faq->getKey() }}" class="faq-header collapsed" data-bs-toggle="collapse" aria-expanded="false" aria-controls="accordion-faq-{{ $faq->getKey() }}">
                                            {!! BaseHelper::clean($faq->question) !!}
                                        </a>
                                        <div id="accordion-faq-{{ $faq->getKey() }}" @class(['collapse', 'show' => $loop->first]) data-bs-parent="#wrapper-faq">
                                            <p class="faq-body">
                                                {!! BaseHelper::clean($faq->answer) !!}
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @break

                    @default
                        @foreach($categories as $category)
                            <div class="tf-faq">
                                <h5>{{ $category->name }}</h5>
                                <ul class="box-faq" id="wrapper-faq-{{ $categorySlug = Str::slug($category->name) }}">
                                    @foreach($category->faqs as $faq)
                                        <li class="faq-item">
                                            <a href="#{{ $categorySlug }}-faq-{{ $faq->getKey() }}" class="faq-header collapsed" data-bs-toggle="collapse" aria-expanded="false" aria-controls="{{ $categorySlug }}-faq-{{ $faq->getKey() }}">
                                                {!! BaseHelper::clean($faq->question) !!}
                                            </a>
                                            <div id="{{ $categorySlug }}-faq-{{ $faq->getKey() }}" class="collapse" data-bs-parent="#wrapper-faq-{{ Str::slug($category->name) }}">
                                                <p class="faq-body">
                                                    {!! BaseHelper::clean($faq->answer) !!}
                                                </p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                @endswitch
            </div>
        </div>
    </div>
</section>
