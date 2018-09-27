<?php

namespace App\Listeners\Order;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Components\Mailer;
use App\Models\Order;
use App\Models\User;
use App\Events\Order\OrderVariantPriceMissedEvent;

class OnOrderVariantPriceMissedEvent
{
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(OrderVariantPriceMissedEvent $event)
    {
        $order = $event->order;
        $variant = $event->variant;

        if (getenv('EMAIL_SUPPORT')) {
            Mailer::sendOrderActionRequiredAdminEmail([
                'order' => $order,
                'action' => Order::ACTION_REQUIRED_VARIANT_PRICE_MISSED,
                'description' => trans('labels.admin_action_required_variant_price_missed_description')
            ]);
        }

        $admins = User::getAdmins();
        if ($admins) {
            foreach ($admins as $user) {
                $this->notifications->create($user, [
                    'icon' => 'fa-shopping-bag',
                    'body' => trans('messages.action_required_for_order_n', [
                        'order' => $order->id
                    ]).': '.$order->getActionRequiredDescription(Order::ACTION_REQUIRED_VARIANT_PRICE_MISSED),
                    'action_text' => trans('actions.open_order'),
                    'action_url' => url('/admin/orders/'.$order->id.'/edit')
                ]);
            }
        }


        // notify user
            Mailer::sendOrderActionRequiredNotificationEmail($event->order->user, [
                'order' => $order,
                'action' => Order::ACTION_REQUIRED_VARIANT_PRICE_MISSED
            ]);

            $this->notifications->create($order->user, [
                'icon' => 'fa-exclamation-triangle',
                'body' => trans('labels.action_required_for_order', [
                    'order_id' => $order->id
                ]).': '.$order->getActionRequiredDescription(Order::ACTION_REQUIRED_VARIANT_PRICE_MISSED),
                'action_text' => trans('actions.open_order'),
                'action_url' => $order->publicShippingUrl()
            ]);
    }
}
