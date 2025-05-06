@if ($categories->isNotEmpty())
    <div class="widget-box bg-surface categories">
        @if ($config['name'])
            <div class="h7 fw-7 text-black">{!! BaseHelper::clean($config['name']) !!}</div>
        @endif

        <ul>
            @foreach ($categories as $category)
                <li>
                    <a href="{{ $category->url }}" class="categories-item">
                        <span>{{ $category->name }}</span>
                        <span>({{ number_format($category->posts_count) }})</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
