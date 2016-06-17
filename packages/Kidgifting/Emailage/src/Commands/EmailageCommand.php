<?php

namespace Kidgifting\Emailage\Commands;

use Illuminate\Console\Command;
use Kidgifting\Emailage\EmailageWrapper;

/**
 * @author: chengxian
 * Date: 3/7/16
 * @copyright Cheng Xian Lim
 */
class EmailageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emailage:validate {--email= : Email to Test} {--ip= : IP to Test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $client;

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
     * @param EmailageWrapper $emailAge
     */
    public function handle(EmailageWrapper $emailAge)
    {
        $email = $this->option('email');
        $ip = $this->option('ip');

        if ( !$this->isValidateInput($email, $ip) ) {
            $this->error('Required: --email --ip');
            return;
        }

        $response = $emailAge->validate($email, $ip);

        dd($response);
    }

    private function isValidateInput($email, $ip) {
        return ( $email && $ip);
    }
}