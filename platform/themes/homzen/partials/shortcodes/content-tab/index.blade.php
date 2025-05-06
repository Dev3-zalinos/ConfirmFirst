<section class="flat-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <ul
                    class="nav-tab-privacy"
                    role="tablist"
                >
                    @foreach ($tabs as $key => $tab)
                        <li
                            class="nav-tab-item"
                            role="presentation"
                        >
                            <a
                                href="#{{ Str::slug($tab['title'], '-') }}"
                                @class(['nav-link-item', 'active' => $loop->first])
                                data-bs-toggle="tab"
                            >
                                {{ $loop->iteration }}. {{ $tab['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-7">
                @if ($shortcode->title)
                    <h5 class="text-capitalize title">
                        {!! BaseHelper::clean($shortcode->title) !!}
                    </h5>
                @endif
                <div class="tab-content content-box-privacy">
                    @foreach ($tabs as $key => $tab)
                        <div
                            @class(['tab-pane fade', 'show active' => $loop->first])
                            id="{{ Str::slug($tab['title'], '-') }}"
                            role="tabpanel"
                        >
                            <h6>{{ $loop->iteration }}. {{ $tab['title'] }}</h6>
                            <p>{!! BaseHelper::clean(nl2br($tab['content'])) !!}</p>
                            @if (isset($tab['list']) && !empty($tab['list']))
                                <ul class="box-list">
                                    @foreach ($tab['list'] as $listItem)
                                        <li>{{ $listItem }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
