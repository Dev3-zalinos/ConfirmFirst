<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Models\Review;
use Botble\RealEstate\Repositories\Interfaces\ReviewInterface;
use Botble\RealEstate\Tables\ReviewTable;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    public function __construct(protected ReviewInterface $reviewRepository)
    {
    }

    public function index(ReviewTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::review.name'));

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/review.css');

        return $dataTable->renderTable();
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $review = Review::query()->findOrFail($id);
            $review->delete();

            event(new DeletedContentEvent(REVIEW_MODULE_SCREEN_NAME, $request, $review));

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
