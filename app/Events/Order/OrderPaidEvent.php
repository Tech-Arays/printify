<?php

namespace App\Events\Order;

use App\Events\Event;
use App\Models\Order;

class OrderPaidEvent extends Event
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }
}
