<ul class="search-suggestion">
    @forelse ($items as $item)
        <li class="search-suggestion-item" data-no-prevent>
            <a href="{{ $item->url }}" class="d-flex align-items-start gap-2">
                <div class="search-suggestion-image">
                    {{ RvMedia::image($item->image, $item->name, 'thumb') }}
                </div>

                <div class="search-suggestion-content">
                    <h6 class="line-clamp-1" title="{{ $item->name }}">{{ $item->name }}</h6>
                    @if($item->short_address)
                        <p>{{ $item->short_address }}</p>
                    @endif
                </div>
            </a>
        </li>
    @empty
        <li class="text-muted text-center">{{ __('No suggestion found') }}</li>
    @endforelse
</ul>
