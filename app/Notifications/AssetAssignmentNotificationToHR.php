<?php

namespace App\Notifications;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\File;

class AssetAssignmentNotificationToHR extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Asset $asset,
        protected Employee $employee,
        protected ?string $pdfPath = null,
        protected string $action = 'assign'
    ) {}
    

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $data = [
            'user'      => $notifiable,
            'asset'     =>$this->asset,
            'employee'  =>$this->employee,
            'assignedBy' => auth()->user()->name,
        ];

    if ($this->action === 'assign') {
        $subject = 'A new asset has been assigned to ' .  $this->employee->user->name;
        $message = (new MailMessage)
            ->subject($subject)
            ->view('email.assetAssignment', $data);

        if (File::exists($this->pdfPath)) {
            $message->attach($this->pdfPath, [
                'as' => 'Old_Policy.pdf', 
                'mime' => 'application/pdf',
            ]);
        } else {
            $message->line('No attachment available.');
        }

        return $message;
    }
}

}
