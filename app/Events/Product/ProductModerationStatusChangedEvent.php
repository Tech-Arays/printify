<?php

namespace App\Events\Product;

use App\Events\Event;
use App\Models\Product;

class ProductModerationStatusChangedEvent extends Event
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
