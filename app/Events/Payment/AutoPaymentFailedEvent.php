<?php

namespace App\Events\Payment;

use App\Events\Event;
use App\Models\Payment;

class AutoPaymentFailedEvent extends Event
{
    public $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }
}
