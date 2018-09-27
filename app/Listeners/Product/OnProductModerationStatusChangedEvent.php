<?php

namespace App\Listeners\Product;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Mailer;
use App\Jobs\Product\ProductPushToStoreJob;
use App\Models\StoreSettings;

class OnProductModerationStatusChangedEvent
{
    public function handle(\App\Events\Product\ProductModerationStatusChangedEvent $event)
    {
        if ($event->product->isApproved()) {

            if (
                $event->product->store->getSetting(
                    StoreSettings::SETTING_AUTO_PUSH_PRODUCTS
                )
                && !$event->product->isSynced()
                && !$event->product->isQueuedForSync()
                && $event->product->store->isInSync() // push to store not used for custom stores
            ) {
                dispatch(new ProductPushToStoreJob($event->product));
                $event->product->queuedForSync();
            }
            else if (!$event->product->store->isInSync()) {
                $event->product->activate();
            }

            Mailer::sendProductApprovedEmail($event->product->user, [
                'product' => $event->product
            ]);
        }
        else if ($event->product->isDeclined()) {
            Mailer::sendProductDeclinedEmail($event->product->user, [
                'product' => $event->product
            ]);
        }
        else if ($event->product->isOnModeration()) {
            Mailer::sendProductOnModerationEmail([
                'product' => $event->product
            ]);
        }
    }
}
