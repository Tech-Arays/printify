<?php

namespace App\Listeners\Order;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Components\Money;
use App\Components\Mailer;
use App\Events\Order\OrderPaidEvent;

class OnOrderPaidEvent
{
    public function handle(OrderPaidEvent $event)
    {
        $order = $event->order;
        $store = $event->order->store;
        
        $store->addCharges(
            Money::i()->amount(
                $order->total()
            )
        );
    }
}
