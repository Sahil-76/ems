<?php

namespace App\Notifications;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AssetAssignmentNotificationToUser extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Asset $asset,
        protected string $action,
        protected Employee $employee,
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $data = [
            'user' => $notifiable,
            'asset'=>$this->asset,
            'employee'=>$this->employee,
            'assignedBy' => auth()->user()->name,
        ];

        if ($this->action === 'assign') {
            $subject = 'A new asset has been assigned to you';
            $message = (new MailMessage)
                ->subject($subject)
                ->view('email.assetAssignment', $data);
            return $message;
        }

        $subject = 'An asset has been unassigned from you';
        return (new MailMessage)
            ->subject($subject)
            ->view('email.assetUnassignment', $data);
    }
}
