<?php

namespace App\Listeners\Payment;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Components\Mailer;
use App\Events\Payment\AutoPaymentFailedEvent;

class OnAutoPaymentFailedEvent
{
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }
    
    public function handle(AutoPaymentFailedEvent $event)
    {
        Mailer::sendAutoPaymentFailedNotificationEmail($event->payment->user, [
            'payment' => $event->payment
        ]);
        
        $order = $event->payment->order;
        $this->notifications->create($order->user, [
            'icon' => 'fa-exclamation-triangle',
            'body' => trans('labels.automatic_payment_failed_for_order', [
                'order_id' => $order->id
            ]).': '.trans('labels.check_order_and_payment_method'),
            'action_text' => trans('actions.open_order'),
            'action_url' => $order->publicUrl()
        ]);
    }
}
