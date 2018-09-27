<?php

namespace App\Events\ProductModelTemplate;

use App\Events\Event;

class ShippingGroupAssignedEvent extends Event
{
    public $templates;

    public function __construct($templates)
    {
        $this->templates = $templates;
    }
}
