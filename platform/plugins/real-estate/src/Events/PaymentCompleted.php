<?php

namespace Botble\RealEstate\Events;

use Botble\Base\Events\Event;
use Botble\Payment\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentCompleted extends Event
{
    use Dispatchable;

    public function __construct(public Payment $payment)
    {
    }
}
