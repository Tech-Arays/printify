<?php

namespace App\Events\User;

use App\Events\Event;
use App\Models\User;
use Illuminate\Http\Request;

class UserCreatedEvent extends Event
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
