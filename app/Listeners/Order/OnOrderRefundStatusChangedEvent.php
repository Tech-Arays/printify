<?php

namespace App\Listeners\Order;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Components\Mailer;
use App\Models\Order;
use App\Models\SupportRequest;
use App\Events\Order\OrderRefundStatusChangedEvent;

class OnOrderRefundStatusChangedEvent
{
    public function handle(OrderRefundStatusChangedEvent $event)
    {
        $order = $event->order;
        
        switch ($order->refund_status) {
            case Order::REFUND_STATUS_REQUESTED:
                
                // notify admin via support requests
                $supportRequest = new SupportRequest();
                $supportRequest->openRefund([
                    'subject' => trans('labels.refund_request'),
                    'text' => trans('labels.refund_request_for_order').' #'.$order->id
                ], [
                    'order_id' => $order->id,
                    'transaction_id' => $order->firstSuccessfulPayment()
                        ? $order->firstSuccessfulPayment()->transaction_id
                        : null
                ]);
                break;
            
            case Order::REFUND_STATUS_REFUNDED:
                // notify order user
                break;
        }
    }
}
