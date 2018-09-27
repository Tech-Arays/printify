<?php

namespace App\Events\User;

use App\Events\Event;
use App\Models\User;

class UserChargedEvent extends Event
{
    public $amount;
    public $user;

    public function __construct($user, $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }
}
