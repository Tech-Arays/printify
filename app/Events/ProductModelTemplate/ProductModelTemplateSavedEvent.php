<?php

namespace App\Events\ProductModelTemplate;

use App\Events\Event;
use App\Models\ProductModelTemplate;

class ProductModelTemplateSavedEvent extends Event
{
    public $product_model_template;

    public function __construct(ProductModelTemplate $product_model_template)
    {
        $this->product_model_template = $product_model_template;
    }
}
