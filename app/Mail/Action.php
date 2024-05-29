<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Action extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $object;
    public $data;
    public $subject;
    public $template;
    public $emailMessage;
    public $link;
    public function __construct($object,$data,$subject,$template)
    {
        // dd($object,$data,$subject,$template);
        $this->object       =   $object;
        $this->data         =   $data;
        $this->subject      =   $subject;
        $this->template     =   $template;
        $this->emailMessage =   $data['message'];
        $this->link         =   $data['link'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->template)
        ->with('object',$this->object)
        ->with('emailMessage',$this->emailMessage)
        ->with('link',$this->link)
        ->with('data',$this->data)
        ->with('subject',$this->subject)
        ->subject($this->subject);
    }
}
