<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\SupportRequest;

class SupportPolicy
{
    use HandlesAuthorization;
    
    public function openTicket(User $user, SupportRequest $supportRequest)
    {
        return (bool)($user->id);
    }
    
}
