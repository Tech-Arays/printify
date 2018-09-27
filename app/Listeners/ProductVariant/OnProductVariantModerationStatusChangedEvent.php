<?php

namespace App\Listeners\ProductVariant;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Mailer;

class OnProductVariantModerationStatusChangedEvent
{
    public function handle(\App\Events\ProductVariant\ProductVariantModerationStatusChangedEvent $event)
    {
        if ($event->variant->isApproved()) {
            Mailer::sendProductVariantApprovedEmail($event->variant->user, [
                'variant' => $event->variant
            ]);
        }
        else if ($event->variant->isDeclined()) {
            Mailer::sendProductVariantDeclinedEmail($event->variant->user, [
                'variant' => $event->variant
            ]);
        }
        else if ($event->variant->isOnModeration()) {
            Mailer::sendProductVariantOnModerationEmail([
                'variants' => [$event->variant]
            ]);
        }
    }
}
