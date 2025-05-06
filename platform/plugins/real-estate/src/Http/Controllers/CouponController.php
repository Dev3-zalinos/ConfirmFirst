<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JsValidation\Facades\JsValidator;
use Botble\RealEstate\Http\Requests\CouponRequest;
use Botble\RealEstate\Models\Coupon;
use Botble\RealEstate\Tables\CouponTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CouponController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::coupon.name'), route('coupons.index'));
    }

    public function index(CouponTable $discountTable): View|JsonResponse
    {
        $this->pageTitle(trans('plugins/real-estate::coupon.name'));

        return $discountTable->renderTable();
    }

    public function create(): View
    {
        $this->pageTitle(trans('plugins/real-estate::coupon.create'));

        Assets::addStyles('timepicker')
            ->addScripts(['timepicker', 'form-validation'])
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/coupon.js');

        $jsValidator = JsValidator::formRequest(CouponRequest::class);

        $coupon = new Coupon();

        return view('plugins/real-estate::coupons.create', compact('jsValidator', 'coupon'));
    }

    public function store(CouponRequest $request)
    {
        $coupon = Coupon::query()->create(array_merge($request->validated(), $request->has('never_expired') ? [] : [
            'expires_date' => $request->date('expires_date')->setTimeFrom($request->input('expires_time')),
        ]));

        event(new CreatedContentEvent(COUPON_MODULE_SCREEN_NAME, $request, $coupon));

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/real-estate::coupon.created_message'))
            ->setNextUrl(route('coupons.edit', $coupon));
    }

    public function edit(Coupon $coupon): View
    {
        Assets::addStyles('timepicker')
            ->addScripts(['timepicker', 'form-validation'])
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/coupon.js');

        $this->pageTitle(trans('plugins/real-estate::coupon.edit', ['name' => $coupon->code]));

        $jsValidator = JsValidator::formRequest(CouponRequest::class);

        return view('plugins/real-estate::coupons.edit', compact('coupon', 'jsValidator'));
    }

    public function update(Coupon $coupon, CouponRequest $request)
    {
        $coupon->update(
            array_merge(
                $request->validated(),
                $request->has('never_expired')
                    ? ['expires_date' => null]
                    : ['expires_date' => $request->date('expires_date')->setTimeFrom($request->input('expires_time'))]
            )
        );

        event(new UpdatedContentEvent(COUPON_MODULE_SCREEN_NAME, $request, $coupon));

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::notices.delete_success_message'))
            ->setNextUrl(route('coupons.index'));
    }

    public function generateCouponCode()
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (Coupon::query()->where('code', $code)->exists());

        return $this
            ->httpResponse()
            ->setData($code);
    }
}
