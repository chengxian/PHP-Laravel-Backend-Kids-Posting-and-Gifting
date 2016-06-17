<?php

use App\Child;
use App\Jobs\SendGiveGiftEmail;
use App\Jobs\SendInviteEmail;
use App\Jobs\SendReceiveGiftEmail;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Testing\TestCase;

use Laracasts\Behat\Context\Services\MailTrap;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Betacode;
use App\Invite;
use App\User;
use App\Setting;

// TODO move to package
require_once 'RestContext.php';

class AuthApiContext extends RestContext implements Context, SnippetAcceptingContext
{
    use DispatchesJobs;
    use MailTrap;
//    use \Laracasts\Behat\Context\DatabaseTransactions;

    protected $user = null;

    private $properties = [];

    /**
     * @static
     * @beforeSuite
     */
    public static function setUpDb()
    {
        Artisan::call('migrate');
    }


    /**
     * @static
     * @beforeFeature
     */
    public static function prepDb()
    {
        //Artisan::call('migrate:refresh');
        //Artisan::call('db:seed');
    }

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct()
    {
        return parent::__construct([
            'base_url' => env('BASE_URL')
        ]);
    }

    /**
     * @Given I have checked to make sure the beta code :arg1 does not exist
     */
    public function iHaveCheckedToMakeSureTheBetaCodeDoesNotExist($arg1)
    {
        $deleted = Betacode::where('betacode', $arg1)->delete();
        TestCase::assertGreaterThanOrEqual(0, $deleted);
    }

    /**
     * @Given I have checked to make sure the invite code :arg1 does not exist
     */
    public function iHaveCheckedToMakeSureTheInviteCodeDoesNotExist($arg1)
    {
        $deleted = Invite::where('invite_code', $arg1)->delete();
        TestCase::assertGreaterThanOrEqual(0, $deleted);
    }

    /**
     * @Given I have a unique beta code :arg1 for :arg2
     */
    public function iHaveAUniqueBetaCodeFor($arg1, $arg2)
    {
        Betacode::where('betacode', $arg1)->delete();

        $betacode = new Betacode;
        $betacode->email = $arg2;
        $betacode->betacode = $arg1;
        $betacode->save();

        $betacode = Betacode::where('betacode', $arg1)->firstOrFail();

        TestCase::assertNotNull($betacode, "Beta code should be retrieved. Did not find " . $arg1);

    }

    /**
     * @Given I have a betacode :arg1
     */
    public function iHaveABetacode($arg1)
    {
        $betacode = Betacode::where('betacode', $arg1)->first();

        TestCase::assertNotNull($betacode, "Beta code should be retrieved. Did not find " . $arg1);
    }

    /**
     * @Given user :arg1 does not exist
     */
    public function userDoesNotExist($arg1)
    {
        $deleted = User::where('email', $arg1)->delete();
        TestCase::assertGreaterThanOrEqual(0, $deleted);
    }

    /**
     * @Given I want to test JWT
     */
    public function iWantToTestJwt()
    {

        $user_data = [
            'email' => "kidgifting@mailinator.com",
            'password' => bcrypt("timtim"),
            'id' => 123456
        ];

//        $user = User::create($user_data);
//        $token = JWTAuth::fromUser($user);
//
//        print("hi");
    }

    /**
     * @Given I have a user with email :arg1
     */
    public function iHaveAUserWithEmail($arg1)
    {
        $user = User::where('email', '=', $arg1)->firstOrFail();
        $this->_token = JWTAuth::fromUser($user);

    }

    /**
     * @Given user with email :arg1 has no settings
     */
    public function userWithEmailHasNoSettings($arg1)
    {
        $user = User::where('email', '=', $arg1)->firstOrFail();
        $user_setting = Setting::where('user_id', $user->id)->delete();
    }

    /**
     * @Then the setting :setting for user with email :email should be :value
     */
    public function theSettingForUserWithEmailShouldBe($setting, $email, $value)
    {
        $user = User::where('email', '=', $email)->firstOrFail();
        $user_setting = Setting::where('user_id', $user->id)->firstOrFail();

        TestCase::assertEquals($user_setting->getAttributeValue($setting), $value);
    }

    /**
     * @Given user with email :email has :count children
     */
    public function userWithEmailHasChildren($email, $count)
    {
        $user = User::where('email', '=', $email)->firstOrFail();

        if ($count ==0) {
            Child::where('parent_id', $user->id)->delete();
        }

        TestCase::assertEquals(Child::where('parent_id', $user->id)->count(), $count);
    }

    /****************************
     * Invites
     */

    /**
     * @When the :key private property is :value
     */
    public function thePrivatePropertyIs($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * @When the system creates an invite job with email :arg1 and invite_code :arg2
     */
    public function theSystemCreatesAnInviteJobWithEmailAndInviteCode($arg1, $arg2)
    {
        $fromUser = User::all()->first();
        
        if ($fromUser == null) {
            throw new Exception("no users");
        }
        
        $job = new SendInviteEmail($arg2, $arg1, $fromUser);
        dispatch($job);
    }

    /**
     * @Then an invite should be sent to :arg1 with the private property :arg2
     */
    public function anInviteShouldBeSentToWithThePrivateProperty($arg1, $arg2)
    {
        $lastEmail = $this->fetchInbox()[0];

        TestCase::assertContains($this->properties[$arg2], $lastEmail->html_body);
    }


    /*******************
     * Gifts
     */

    /**
     * @When the system creates an gift job with email
     */
    public function theSystemCreatesAnGiftJobWithEmail()
    {
        $user = User::all()->first();

        if ($user == null) {
            throw new Exception("no users");
        }

        $child = Child::all()->first();

        if ($child == null) {
            throw new Exception("no kids");
        }

        $job = new SendGiveGiftEmail($user, $user, $child, $this->properties['amount']);
        dispatch($job);
    }

    /**
     * @Then an invite should be sent with the private property :arg1
     */
    public function anInviteShouldBeSentWithThePrivateProperty($arg1)
    {
        $lastEmail = $this->fetchInbox()[0];

        TestCase::assertContains($this->properties[$arg1], $lastEmail->html_body);
    }

    /**
     * @When the system creates an gift receive job with email
     */
    public function theSystemCreatesAnGiftReceiveJobWithEmail()
    {
        $user = User::all()->first();

        if ($user == null) {
            throw new Exception("no users");
        }

        $child = Child::all()->first();

        if ($child == null) {
            throw new Exception("no kids");
        }

        $job = new SendReceiveGiftEmail($user, $user, $child, $this->properties['amount']);
        dispatch($job);
    }
}
