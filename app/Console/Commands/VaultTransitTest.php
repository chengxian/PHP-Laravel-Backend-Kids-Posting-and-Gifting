<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kidgifting\ThinTransportVaultClient\TransitClient;
use App\User;
use Log;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class VaultTransitTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vault:transittest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Vault Transit Client';

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
    public function handle()
    {
//        $client = new TransportClient("http://192.168.20.20:8200", "9a6f5351-1e5f-fde6-16a4-6cf908302e3d");
//
//        $cipher = $client->encrypt("mysql-laravel5", "something something friday", "tim");
//
//        Log::debug($cipher);
//
//        $value = $client->decrypt("mysql-laravel5", $cipher, "tim");
//
//        Log::debug($value);

        Log::debug("\n\n\n\n ***************************** \n");
        User::where('email','hiA@goo.com')->delete();
        User::where('email','hiA2@goo.com')->delete();
//        $user = User::create(['driver_licence' => "plain string",
//        'email' => 'hi@hi.com']);
        $user = new User;
        $user->email = "hiA@goo.com";
        $user->driver_licence = "plain string";
        $user->save();

        $user = new User;
        $user->email = "hiA2@goo.com";
        $user->driver_licence = "plain string2";
        $user->save();

//        Log::debug($user->driver_licence . "\n");

        /*
         * test encrypt/decrypt
         */
        $check = User::where('email','hiA@goo.com')->firstOrFail();
        Log::debug("check: " . $check->driver_licence);

        /*
         * should not decrypt
         */
        $check2 = User::where('email','hiA@goo.com')->get();

        Log::debug("check2, should not have decrypted \n");

        /*
        * should not decrypt
        */
        $check3 = User::where('email','hiA@goo.com')->get();

        foreach ($check3 as $checkk) {
            Log::debug($checkk->id);
        }

        Log::debug("check3 not should have decrypted");

        /*
        * should decrypt
        */
        $check4 = User::where('email','hiA@goo.com')->get();

        foreach ($check4 as $checkk) {
            Log::debug($checkk->driver_licence);
        }

        Log::debug("check4 should have decrypted");


    }
}
