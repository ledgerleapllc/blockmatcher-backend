<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use App\Mail\Notification;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to = null;
    protected $headline = '';
    protected $content = '';
    protected $button = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $headline, $content, $button)
    {
        $this->to = $to;
        $this->headline = $headline;
        $this->content = $content;
        $this->button = $button;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->to)->send(new Notification($this->headline, $this->content, $this->button));
    }
}
