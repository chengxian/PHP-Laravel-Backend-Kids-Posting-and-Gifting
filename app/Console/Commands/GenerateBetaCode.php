<?php

namespace App\Console\Commands;

use App;
use App\Betacode;
use App\KFMail;
use Illuminate\Console\Command;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class GenerateBetaCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kidgifting:generatebetacode {email}';

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
     * @param KFMail $mail
     * @return mixed
     */
    public function handle(KFMail $mail)
    {
        $email = $this->argument('email');


        $betacode = new Betacode;
        $code = $betacode->generateCode();
        $betacode->email = $email;
        $betacode->betacode = $code;
        $betacode->save();


        $to = $email;

        $mail->sendBetaCodeMail($to, $code);
    }
}
