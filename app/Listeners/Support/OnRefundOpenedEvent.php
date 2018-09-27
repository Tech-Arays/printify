<?php

namespace App\Listeners\Support;

use Mailer;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Events\Support\RefundOpenedEvent;
use App\Models\User;

class OnRefundOpenedEvent
{
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }
    
    public function handle(RefundOpenedEvent $event)
    {
        Mailer::sendUserTicketOpenedEmail($event->supportRequest->user, [
            'supportRequest' => $event->supportRequest
        ]);
        
        if (getenv('EMAIL_SUPPORT')) {
			Mailer::sendAdminTicketOpenedEmail([
                'supportRequest' => $event->supportRequest
            ]);
		}
        
        $admins = User::getAdmins();
        if ($admins) {
            foreach ($admins as $user) {
                $this->notifications->create($user, [
                    'icon' => 'fa-dollar',
                    'body' => trans('messages.refund_requested_admin_email_subject'),
                    'action_text' => trans('actions.open_refund_request'),
                    'action_url' => url('/admin/support/'.$event->supportRequest->id.'/show')
                ]);
            }
        }
    }
}
