<?php

namespace App\Listeners\ProductModelTemplate;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Order;
use App\Events\ProductModelTemplate\ShippingGroupAssignedEvent;

class OnShippingGroupAssignedEvent
{
    public function handle(ShippingGroupAssignedEvent $event)
    {
        foreach($event->templates as $template) {
            foreach($template->models as $model) {
                foreach($model->variants as $variant) {
                    foreach($variant->orders as $order) {
                        if (
                            in_array(Order::ACTION_REQUIRED_SHIPPING_GROUP_ASSIGN, $order->action_required)
                            && $order->areAllShippingGroupsAssigned()
                        ) {
                            $order->resolveActionRequired(
                                Order::ACTION_REQUIRED_SHIPPING_GROUP_ASSIGN
                            );
                        }
                    }
                }
            }
        }
    }
}
