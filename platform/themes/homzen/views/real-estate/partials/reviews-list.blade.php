@if($reviews->isNotEmpty())
    <ul class="box-review">
        @foreach($reviews as $review)
            <li class="list-review-item">
                <div class="avatar avt-60 round">
                    {{ RvMedia::image($review->author->avatar_url, $review->author->name) }}
                </div>
                <div class="content w-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="name h7 fw-7 text-black">
                            {{ $review->author->name }}
                        </div>
                        <span class="mt-4 d-inline-block date body-3 text-variant-2">
                    {{ Theme::formatDate($review->created_at) }}
                </span>
                    </div>

                    @include(Theme::getThemeNamespace('views.real-estate.partials.star'), ['class' => 'mt-8', 'star' => $review->star])

                    <p class="mt-12 body-2 text-black">
                        {!! BaseHelper::clean(nl2br($review->content)) !!}
                    </p>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted text-center h6 mt-3">{{ __('Looks like there are no reviews!') }}</p>
@endif

@if($reviews->hasPages())
    <div class="pagination d-flex justify-content-center mt-5">
        {{ $reviews->onEachSide(1)->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
    </div>
@endif
