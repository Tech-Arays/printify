<?php

namespace App\Events\Store;

use App\Events\Event;
use App\Models\Product;

class ProductCreatedEvent extends Event
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
