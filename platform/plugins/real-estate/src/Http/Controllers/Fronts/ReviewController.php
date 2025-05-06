<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\ReviewRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReviewController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            abort_unless(RealEstateHelper::isEnabledReview(), 404);

            return $next($request);
        });
    }

    public function index(string $key, Request $request)
    {
        $request->validate([
            'reviewable_type' => Rule::in([Property::class, Project::class]),
        ]);

        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix($request->query('reviewable_type')));

        abort_unless($slug, 404);

        $reviewable = $slug->reference;

        abort_unless($reviewable, 404);

        $reviews = $reviewable
            ->reviews()
            ->where('status', ReviewStatusEnum::APPROVED)
            ->with(['author', 'author.avatar'])
            ->latest()
            ->paginate((int) setting('real_estate_reviews_per_page', 10) ?: 10);

        return $this
            ->httpResponse()
            ->setData(view(
                Theme::getThemeNamespace('views.real-estate.partials.reviews-list'),
                compact('reviews')
            )->render());
    }

    public function store(string $key, ReviewRequest $request)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix($request->input('reviewable_type')));

        abort_unless($slug, 404);

        $reviewable = $slug->reference;

        abort_unless($reviewable, 404);

        /** @var Account $account */
        $account = auth('account')->user();

        if (! $account->canReview($reviewable)) {
            throw ValidationException::withMessages([
                'content' => __('You have already submitted a review.'),
            ]);
        }

        $review = $reviewable->reviews()->create([
            ...$request->validated(),
            'account_id' => $account->getKey(),
        ]);

        event(new CreatedContentEvent(REVIEW_MODULE_SCREEN_NAME, $request, $review));

        $reviewsCount = $reviewable->reviews->count();

        return $this
            ->httpResponse()
            ->setMessage(__('Your review has been submitted!'))
            ->setData([
                'count' => $reviewsCount === 1 ? __('1 Review') : __(':number Reviews', ['number' => $reviewsCount]),
                'message' => __('Your review has been submitted!'),
            ]);
    }
}
