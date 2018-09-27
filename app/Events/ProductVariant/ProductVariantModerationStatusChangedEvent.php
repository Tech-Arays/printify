<?php

namespace App\Events\ProductVariant;

use App\Events\Event;
use App\Models\ProductVariant;

class ProductVariantModerationStatusChangedEvent extends Event
{
    public $variant;

    public function __construct(ProductVariant $variant)
    {
        $this->variant = $variant;
    }
}
