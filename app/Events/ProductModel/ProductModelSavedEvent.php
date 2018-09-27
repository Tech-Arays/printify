<?php

namespace App\Events\ProductModel;

use App\Events\Event;
use App\Models\ProductModel;

class ProductModelSavedEvent extends Event
{
    public $product_model;

    public function __construct(ProductModel $product_model)
    {
        $this->product_model = $product_model;
    }
}
