<?php

namespace App\Jobs;

use App\KFMail;
use App\User;
use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRequestBetacodeEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @param KFMail $mail
     */
    public function handle(KFMail $mail)
    {
        $from = $this->email;
        $subject = "Request Betacode";
        $data = [
            'title' => 'Hi Kidgifting',
            'body' => 'Please give me betacode.'
        ];

        $content = view('emails.requestbetacode', $data)->render();
        $mail->sendRequestBetacodeMail($from, $subject, $content);
    }
}
