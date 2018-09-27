<?php

namespace App\Listeners\User;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Mailer;

class OnUserCreatedEvent
{
    public function handle(\App\Events\User\UserCreatedEvent $event)
    {
        Mailer::sendWelcomeEmail($event->user);
        Mailer::sendUserRegisteredMail($event->user);
    }
}
