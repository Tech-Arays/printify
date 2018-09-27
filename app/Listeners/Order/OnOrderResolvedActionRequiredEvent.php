<?php

namespace App\Listeners\Order;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Components\Mailer;
use App\Models\Order;
use App\Models\User;
use App\Events\Order\OrderResolvedActionRequiredEvent;

class OnOrderResolvedActionRequiredEvent
{
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }
    
    public function handle(OrderResolvedActionRequiredEvent $event)
    {
        $order = $event->order;
        
        switch($event->action) {
            case Order::ACTION_REQUIRED_SHIPPING_METHOD:
            case Order::ACTION_REQUIRED_AUTO_ORDER_AMOUNT_REACHED:
                break;
            
            case Order::ACTION_REQUIRED_SHIPPING_GROUP_ASSIGN:
                
                // notify user
                Mailer::sendOrderCanBeCompletedEmail($event->order->user, [
                    'order' => $order
                ]);
                
                $this->notifications->create($order->user, [
                    'icon' => 'fa-thumbs-up',
                    'body' => trans('messages.order_can_be_completed', [
                        'order_id' => $order->id
                    ]),
                    'action_text' => trans('actions.open_order'),
                    'action_url' => $order->publicShippingUrl()
                ]);
                
                break;
        }
    }
}

