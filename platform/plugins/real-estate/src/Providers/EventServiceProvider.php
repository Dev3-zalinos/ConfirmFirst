<?php

namespace Botble\RealEstate\Providers;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Payment\Events\PaymentWebhookReceived;
use Botble\RealEstate\Events\PaymentCompleted;
use Botble\RealEstate\Listeners\AddSitemapListener;
use Botble\RealEstate\Listeners\SubscribedPackageListener;
use Botble\RealEstate\Listeners\UpdatedContentListener;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        RenderingSiteMapEvent::class => [
            AddSitemapListener::class,
        ],
        PaymentWebhookReceived::class => [
            SubscribedPackageListener::class,
        ],
        PaymentCompleted::class => [
            SubscribedPackageListener::class,
        ],
    ];
}
