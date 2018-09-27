<?php

namespace App\Events\Support;

use App\Events\Event;
use App\Models\SupportRequest;

class TicketOpenedEvent extends Event
{
    public $supportRequest;

    public function __construct(SupportRequest $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }
}
