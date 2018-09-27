<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class NotificationPolicy
{
    use HandlesAuthorization;
    
    public function delete(User $user, $notification)
    {
        return $user->isOwnerOf($notification);
    }
}
