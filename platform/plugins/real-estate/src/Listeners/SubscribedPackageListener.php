<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Facades\EmailHandler;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Events\PaymentWebhookReceived;
use Botble\Payment\Models\Payment;
use Botble\RealEstate\Events\PaymentCompleted;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Transaction;

class SubscribedPackageListener
{
    public function handle(PaymentWebhookReceived|PaymentCompleted $event)
    {
        if ($event instanceof PaymentCompleted) {
            $payment = $event->payment;
        } else {
            $payment = Payment::query()->where('charge_id', $event->chargeId)->first();
        }

        if (! $payment) {
            return;
        }

        $packageId = $payment->order_id;

        if (! $packageId) {
            return;
        }

        $package = Package::query()->find($packageId);

        if (! $package) {
            return;
        }

        $accountId = $payment->customer_id;

        if (! $accountId) {
            return;
        }

        $account = Account::query()->whereKey($accountId)->first();

        if (! $account) {
            return;
        }

        if (Transaction::query()->where('account_id', $account->getKey())->where('payment_id', $payment->id)->exists()) {
            return;
        }

        if (($payment->status == PaymentStatusEnum::COMPLETED)) {
            $account->credits += $package->number_of_listings;
            $account->save();

            $account->packages()->attach($package);
        }

        Transaction::query()->create([
            'user_id' => 0,
            'account_id' => $account->getKey(),
            'credits' => $package->number_of_listings,
            'payment_id' => $payment?->id,
        ]);

        $emailHandler = EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'account_name' => $account->name,
                'account_email' => $account->email,
                'package_name' => $package->name,
                'package_price' => $package->price,
                'package_percent_discount' => $package->percent_save,
                'package_number_of_listings' => $package->number_of_listings,
                'package_price_per_credit' => $package->price ? $package->price / ($package->number_of_listings ?: 1) : 0,
            ]);

        if (! $package->price) {
            $emailHandler->sendUsingTemplate('free-credit-claimed');
        } else {
            $emailHandler->sendUsingTemplate('payment-received');
        }

        $emailHandler->sendUsingTemplate('payment-receipt', $account->email);
    }
}
