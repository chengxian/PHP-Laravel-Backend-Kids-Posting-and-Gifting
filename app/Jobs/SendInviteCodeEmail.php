<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use App\User;
use App\Invite;

use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInviteCodeEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $email, $code, $from_user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($from_user, $email)
    {
        $this->from_user = $from_user;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('kidgifting:generateinvitecode', [
            'email' => $this->email,
            'from_user' => $this->from_user
        ]);
    }
}
