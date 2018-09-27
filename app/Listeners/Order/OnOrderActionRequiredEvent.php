<?php

namespace App\Listeners\Order;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Components\Mailer;
use App\Models\Order;
use App\Models\User;
use App\Events\Order\OrderActionRequiredEvent;

class OnOrderActionRequiredEvent
{
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(OrderActionRequiredEvent $event)
    {
        $order = $event->order;

        switch($event->action) {
            case Order::ACTION_REQUIRED_SHIPPING_METHOD:
            case Order::ACTION_REQUIRED_AUTO_ORDER_AMOUNT_REACHED:
                break;

            // notify admin
            case Order::ACTION_REQUIRED_SHIPPING_GROUP_ASSIGN:

                if (getenv('EMAIL_SUPPORT')) {
                    Mailer::sendOrderActionRequiredAdminEmail([
                        'order' => $order,
                        'action' => $event->action,
                        'description' => trans('labels.admin_action_required_shipping_group_assign_description')
                    ]);
                }

                $admins = User::getAdmins();
                if ($admins) {
                    foreach ($admins as $user) {
                        $this->notifications->create($user, [
                            'icon' => 'fa-shopping-bag',
                            'body' => trans('messages.action_required_for_order_n', [
                                'order' => $order->id
                            ]).': '.$order->getActionRequiredDescription($event->action),
                            'action_text' => trans('actions.open_order'),
                            'action_url' => url('/admin/orders/'.$order->id.'/edit')
                        ]);
                    }
                }

                break;
        }

        // notify user
            Mailer::sendOrderActionRequiredNotificationEmail($event->order->user, [
                'order' => $order,
                'action' => $event->action
            ]);

            $this->notifications->create($order->user, [
                'icon' => 'fa-exclamation-triangle',
                'body' => trans('labels.action_required_for_order', [
                    'order_id' => $order->id
                ]).': '.$order->getActionRequiredDescription($event->action),
                'action_text' => trans('actions.open_order'),
                'action_url' => $order->publicShippingUrl()
            ]);
    }
}
