@switch($sidebar)
    @case('inner_footer_sidebar')
        <div class="col-lg-2 col-md-4 col-6">
            <div class="footer-cl-3">
                @if ($config['name'])
                    <div class="fw-7 text-white">{!! BaseHelper::clean($config['name']) !!}</div>
                @endif
                <ul class="mt-10 navigation-menu-footer">
                    @foreach ($items as $item)
                        <li>
                            <a
                                href="{{ url((string) $item->url) }}"
                                title="{{ $item->label }}"
                                @if ($item->is_open_new_tab) target="_blank" @endif
                                {!! $item->attributes ? BaseHelper::clean($item->attributes) : null !!}
                                class="caption-1 text-variant-2"
                            >
                                {{ $item->label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @break

    @default
        <ul class="menu-bottom">
            @foreach($items as $item)
                <li>
                    <a
                        href="{{ url((string) $item->url) }}"
                        title="{{ $item->label }}"
                        @if ($item->is_open_new_tab) target="_blank" @endif
                        {!! $item->attributes ? BaseHelper::clean($item->attributes) : null !!}
                    >
                        {{ $item->label }}
                    </a>
                </li>
            @endforeach
        </ul>
        @break
@endswitch
