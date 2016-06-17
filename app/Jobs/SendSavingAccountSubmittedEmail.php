<?php

namespace App\Jobs;

use Mail;

use App\User;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSavingAccountSubmittedEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $toUser = "";
        if (isset($this->user->first_name) && $this->user->first_name != '') {
            $toUser = $this->user->first_name;
        }
        else {
            $toUser = $this->user->email;
        }

        $toEmail = $this->user->email;

        $subject = "Saving Account Submitted";
        $data = [
            'title' => 'Hi ' . $toUser,
            'body' => 'Your saving account was successfully submitted.'
        ];

        Mail::send('emails.savingaccount_submit', $data, function($message) use($toEmail, $subject) {
            $message->from('admin@kidgifting.com', 'kidgifting Administrator');
            $message->to($toEmail)->subject($subject);
        });
    }
}
