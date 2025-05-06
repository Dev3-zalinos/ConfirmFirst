@if ($paginator->hasPages())
    <ul class="flat-pagination">
        @if (! $paginator->onFirstPage())
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" class="page-numbers" aria-label="{{ trans('pagination.previous') }}">
                    <x-core::icon name="ti ti-chevron-left" />
                </a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li>
                    <span class="page-numbers current">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li>
                        <a href="{{ $url }}" @class(['page-numbers', 'current' => $page == $paginator->currentPage()])>{{ $page }}</a>
                    </li>
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" class="page-numbers" aria-label="{{ trans('pagination.next') }}">
                    <x-core::icon name="ti ti-chevron-right" />
                </a>
            </li>
        @endif
    </ul>
@endif
