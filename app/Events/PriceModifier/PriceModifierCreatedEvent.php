<?php

namespace App\Events\PriceModifier;

use App\Events\Event;
use App\Models\PriceModifier;

class PriceModifierCreatedEvent extends Event
{
    public $price_modifier;

    public function __construct(PriceModifier $price_modifier)
    {
        $this->price_modifier = $price_modifier;
    }
}
