@if ($posts->isNotEmpty())
    <div class="widget-box bg-surface recent">
        @if($config['name'])
            <div class="h7 fw-7 text-black">{{ $config['name'] }}</div>
        @endif

        <ul>
            @foreach ($posts as $post)
                <li>
                    <a href="{{ $post->url }}" class="recent-post-item not-overlay hover-img">
                        <div class="img-style">
                            {{ RvMedia::image($post->image, $post->name, 'medium-square') }}
                        </div>
                        <div class="content">
                            <span class="subtitle">{{ Theme::formatDate($post->created_at) }}</span>
                            <div class="title">{!! BaseHelper::clean($post->name) !!}</div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
