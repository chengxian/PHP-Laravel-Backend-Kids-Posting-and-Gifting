<?php

namespace App\Console\Commands;

use App\User;
use DwollaSwagger\ApiException;
use Illuminate\Console\Command;
use Kidgifting\DwollaWrapper\DwollaWrapperCustomerClient;
use Kidgifting\DwollaWrapper\Models\DwollaSourceAccount;
use Kidgifting\DwollaWrapper\Models\DwollaVerifiedCustomer;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class TestGifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kidgifting:testgifts';

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
     * @param DwollaWrapperCustomerClient $dwollaClient
     * @return mixed
     */
    public function handle(DwollaWrapperCustomerClient $dwollaClient)
    {
        $user = User::firstOrNew(['email' => "kidgiftingff@mailinator.com"]); // example
        $user->password = bcrypt("doesntmatter");
        $user->first_name = "Uncle";
        $user->last_name = "Bob";
        $user->street = '156 5th Ave';
        $user->street1 = '2nd Floow';
        $user->city = 'New York';
        $user->state = 'NY';
        $user->postcode = '10010';
        $user->country = 'USA';
        $user->phone = '6464707019';
        $user->dob = '1900-06-05';
        $ssn = rand(100000000,999999999);
        $user->is_parent = false;

        /*
         * TODO NEED TO TALK TO DWOLLA WHY THIS ISN"T WORKING
         */
//        if ($user->wasRecentlyCreated) {
//            $response = $dwollaClient->createUnverifiedCustomer($user->first_name, $user->last_name, $user->email);
//            dd($response);
//        }

        /*
        * Create Verified Dwolla user
        * https://docs.google.com/document/d/1udXoBJiWx0fCBDqvTAsG7sZMuwQ_eu6m9_paDDfAkPg/edit#heading=h.9m4iulxxqpf1
        * THROWS
        */
        $dwollaCustomerId = null;
        try {
            $dwollaCustomerId = $dwollaClient->createVerifiedCustomer(
                $user->first_name,
                $user->last_name,
                $user->email,
                $user->street,
                $user->street1,
                $user->city,
                $user->state,
                $user->postcode,
                $user->dob,
                $user->phone,
                $ssn,
                '127.0.0.1',
                null
            );
        } catch (ApiException $e) {
            // Customer already exists (checks email). this shouldn't happen outside of testing.... If it does, bad..
            $isEmailDupe = $dwollaClient::hasErrorCodes($e, 'ValidationError', 'Duplicate', "/email");
            print("is email dupe");
            if ($isEmailDupe) {
                $list = $dwollaClient->lizt(100);
                foreach ($list->_embedded->customers as $c) {
                    if ($c->email == $user->email) {
                        $updatedCustomer = $dwollaClient->updateCustomer(
                            $c->id,
                            $user->email,
                            $user->street,
                            $user->street1,
                            $user->city,
                            $user->state,
                            $user->postcode,
                            $user->phone,
                            '127.0.0.1');

                        $dwollaCustomerId = $updatedCustomer->_links['self']->href;
                    }
                }
            }

            // something else went wrong besides customer already existing
            if (!$dwollaCustomerId) {
                throw $e;
            }
        }

        // create verified dwolla customer for the parent
        // TODO check if customer actually verified in Dwolla. Webhook?
        $dwollaCustomer = DwollaVerifiedCustomer::firstOrNew([
            'dwolla_id_hashed' => bcrypt($dwollaCustomerId)
        ]);
        $dwollaCustomer->dwolla_id = $dwollaCustomerId;
        $dwollaCustomer->save();

        // associate the verified dwolla account with the parent
        $user->dwollaCustomer()->associate($dwollaCustomer);
        $user->save();

        $dwollaFundingSource = DwollaSourceAccount::whereType('source')->first();
        $user->fundingAccounts()->save($dwollaFundingSource);

    }
}
