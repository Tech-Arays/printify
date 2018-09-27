<?php

namespace App\Events\Order;

use App\Events\Event;

class OrderVariantPriceMissedEvent extends Event
{
    public $order;
    public $variant;

    public function __construct($order, $variant)
    {
        $this->order = $order;
        $this->variant = $variant;
    }
}
