<?php

namespace App\Events\Store;

use App\Events\Event;
use App\Models\Store;

class StoreCreatedEvent extends Event
{
    public $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }
}
