<?php

namespace App\Listeners\User;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Mailer;

use App\Models\User;

class OnUserStatusChangedEvent
{
    public function handle(\App\Events\User\UserStatusChangedEvent $event)
    {
        $user = $event->user;
        
        switch($event->user->status) {
            case User::STATUS_ACTIVE:
                Mailer::sendUserActivatedEmail($user);
                break;
            
            case User::STATUS_BANNED:
                
                break;
        }
    }
}
