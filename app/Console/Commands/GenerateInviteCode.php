<?php

namespace App\Console\Commands;

use App;
use App\Invite;
use App\KFMail;

use Illuminate\Console\Command;

use Log;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class GenerateInviteCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kidgifting:generateinvitecode {email} {from_user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(KFMail $mail)
    {
        $email = $this->argument('email');
        $from_user_id = $this->argument('from_user');
        
        $invite = new Invite;
        $code = $invite->generateCode();
        $invite->email = $email;
        $invite->invite_code = $code;
        $invite->from_user_id = $from_user_id;
        $invite->save();

        $to = $email;

        $mail->sendInviteCodeMail($to, $code);

    }
}
