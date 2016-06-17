<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use Mail;

use App\User;
use App\Betacode;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBetaCodeEmail extends Job implements ShouldQueue
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
     * @return void
     */
    public function handle()
    {
        Artisan::call('kidgifting:generatebetacode', [
            'email' => $this->email
        ]);
    }
}
