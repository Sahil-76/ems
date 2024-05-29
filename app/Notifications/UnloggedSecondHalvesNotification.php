<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Arr;

class UnloggedSecondHalvesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $date;
    protected $employee;
    protected $cc;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $date, $employee, $cc)
    {
        $this->date     = $date;
        $this->employee = $employee;
        $this->cc       = Arr::wrap($cc);
    }
    

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = [
            'user' => $notifiable,
            'employee' => $this->employee,
            'date' => Carbon::parse($this->date)->format('d-m-y'),
        ];
        $subject = 'Second-Half absent '.  $this->employee->name;
        return (new MailMessage)
        ->subject($subject)
        ->cc($this->cc)
        ->view('email.MissingsecondHalf', $data);
    }
}
