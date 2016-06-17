<?php

namespace App\Jobs;

use App\KFMail;
use App\User;
use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRegisterEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        Log::info('register email: ' . $user->email);
    }

    /**
     * Execute the job.
     *
     * @param KFMail $mail
     */
    public function handle(KFMail $mail)
    {
        $to = $this->user['email'];
        $subject = "Sigup kidgifting";
        $data = [
            'title' => 'Hi ' . $to,
            'body' => 'Congratulations! You successfully signed up kidgifting.'
        ];

        $content = view('emails.register', $data)->render();

        $mail->sendTemplatedMail($to, $subject, $content);
    }
}
