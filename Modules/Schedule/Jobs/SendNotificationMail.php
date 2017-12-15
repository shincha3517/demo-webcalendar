<?php

namespace Modules\Schedule\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Modules\Schedule\Mail\ReliefMail;

class SendNotificationMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    protected $teacher;
    protected $body;

    public $tries = 1;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
//        sleep(3);
        $emailTo = [];
        array_push($emailTo,$this->teacher->email);

        $email = new ReliefMail($this->teacher, $this->body);
        Mail::to($emailTo)->send($email);

//        echo 'End send email';
    }

}
