<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    protected $headline = '';
    protected $content = '';
    protected $button = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($headline, $content, $button)
    {
        $this->headline = $headline;
        $this->content = $content;
        $this->button = $button;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.notification', [
            'headline' => $this->headline,
            'content' => $this->content,
            'button' => $this->button
        ]);
    }
}
