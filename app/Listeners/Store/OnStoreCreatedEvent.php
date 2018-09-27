<?php

namespace App\Listeners\Store;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Mailer;

class OnStoreCreatedEvent
{
    public function handle(\App\Events\Store\StoreCreatedEvent $event)
    {
        Mailer::sendStoreIntegrationWelcomeEmail($event->store->user, [
            'store' => $event->store
        ]);
    }
}
