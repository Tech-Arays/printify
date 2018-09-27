<?php

namespace App\Events\ProductModel;

use App\Events\Event;

class ProductModelInventoryStatusChangedEvent extends Event
{
    public $inventoryStatus;
    public $models;

    public function __construct($inventoryStatus, array $models = [])
    {
        $this->inventoryStatus = $inventoryStatus;
        $this->models = $models;
    }
}
