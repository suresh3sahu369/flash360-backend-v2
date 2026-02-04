<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $messageContent;

    public function __construct($subject, $content)
    {
        $this->subjectText = $subject;
        $this->messageContent = $content;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.custom');
    }
}