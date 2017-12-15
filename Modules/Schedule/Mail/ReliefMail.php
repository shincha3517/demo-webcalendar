<?php

namespace Modules\Schedule\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReliefMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $teacher;
    protected $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($teacher,$body)
    {
        //
        $this->teacher = $teacher;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject  = "Smart Relief Notification";
        return $this->view('emails.relief')->with('teacher', $this->teacher)->with('body',$this->body)->subject($subject);
    }
}
