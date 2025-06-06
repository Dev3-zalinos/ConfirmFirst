<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Exception;

class UpdatedContentListener
{
    public function handle(UpdatedContentEvent $event): void
    {
        try {
            if ($event->data->id &&
                $event->data->author_type === Account::class &&
                auth('account')->check() &&
                $event->data->author_id == auth('account')->id()
            ) {
                app(AccountActivityLogInterface::class)->createOrUpdate([
                    'action' => 'your_property_updated_by_admin',
                    'reference_name' => $event->data->name,
                    'reference_url' => route('public.account.properties.edit', $event->data->id),
                ]);
            }
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
    }
}
