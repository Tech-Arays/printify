<?php

namespace App\Listeners\User;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Components\Mailer;
use App\Events\User\UserChargedEvent;

class OnUserChargedEvent
{
    public function handle(UserChargedEvent $event)
    {
        Mailer::sendPaymentReceiptEmail($event->user, [
            'amount' => $event->amount
        ]);
    }
}
