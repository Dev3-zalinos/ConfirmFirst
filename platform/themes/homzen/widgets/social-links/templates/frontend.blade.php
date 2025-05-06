@if($items = Theme::getSocialLinks())
    <div class="wd-social">
        <span>{!! BaseHelper::clean($config['title']) !!}</span>
        <ul class="list-social d-flex align-items-center">
            @foreach($items as $item)
                <li>
                    <a title="{{ $item->getName() }}" href="{{ $item->getUrl() }}" class="box-icon w-40 social">
                        {!! $item->getIconHtml(['style' => 'width: 1.25rem; height: 1.25rem; stroke-width: 2']) !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
