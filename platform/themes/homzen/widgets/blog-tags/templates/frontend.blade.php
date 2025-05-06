@if ($tags->isNotEmpty())
    <div class="widget-box bg-surface tag">
        @if ($config['name'])
            <div class="h7 fw-7 text-black">{!! BaseHelper::clean($config['name']) !!}</div>
        @endif

        <ul>
            @foreach ($tags as $tag)
                <li>
                    <a href="{{ $tag->url }}" class="tag-item">{{ $tag->name }}</a>
                </li>
            @endforeach
        </ul>

    </div>
@endif
