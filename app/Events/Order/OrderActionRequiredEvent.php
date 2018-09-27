<?php

namespace App\Events\Order;

use App\Events\Event;
use App\Models\Order;

class OrderActionRequiredEvent extends Event
{
    public $order;
    public $action;

    public function __construct($order, $action)
    {
        $this->order = $order;
        $this->action = $action;
    }
}
