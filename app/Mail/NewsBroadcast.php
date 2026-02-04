<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\News;

class NewsBroadcast extends Mailable
{
    use Queueable, SerializesModels;

    public $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    public function build()
    {
        return $this->subject('🔥 Breaking News: ' . $this->news->title)
                    ->view('emails.news_broadcast');
    }
}