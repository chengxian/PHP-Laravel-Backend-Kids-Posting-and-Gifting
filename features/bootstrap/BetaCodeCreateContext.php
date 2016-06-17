<?php

use App\Betacode;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Illuminate\Foundation\Testing\TestCase;
use Laracasts\Behat\Context\Services\MailTrap;

/**
 * Defines application features from the specific context.
 */
class BetaCodeCreateContext extends MinkContext implements Context, SnippetAcceptingContext
{

    //use \Laracasts\Behat\Context\DatabaseTransactions;
    use MailTrap;

    private $user;
    private $betaCode;
    private $wouldBeDuplicatedBetaCode;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        // TODO create known admin
        $user = \App\User::where('email', '=', 'timothy.broder@gmail.com')->firstOrFail();
        Auth::loginUsingId($user->id);
    }

    /**
     * @Then a beta code should be generated for :arg1
     */
    public function aBetaCodeShouldBeGeneratedFor($arg1)
    {
        $betacode = Betacode::where('email', $arg1)->firstOrFail();

        TestCase::assertNotNull($betacode, "Beta code should be retrieved. Did not find one for " . $arg1);

        $betacode = new Betacode;
        $betacode->email = "testies@foo.com";
        $betacode->betacode = Betacode::generateBetaCode();
        $betacode->save();

        $this->wouldBeDuplicatedBetaCode = $betacode->betacode;
    }

    /**
     * @Then this beta code should be unique compared to all beta codes in the system
     */
    public function thisBetaCodeShouldBeUniqueComparedToAllBetaCodesInTheSystem()
    {
        $betacode = new Betacode;
        $betacode->email = uniqid() . "@mailinator.com";
        $betacode->betacode = $this->wouldBeDuplicatedBetaCode;
        $betacode->save();

        $betacodes = Betacode::where('betacode', $this->wouldBeDuplicatedBetaCode);

        TestCase::assertEquals($betacodes->count(), 1, "Duplicate beta code detected");
    }

    /**
     * @Then an email should be sent to the :arg1 with the beta code
     */
    public function anEmailShouldBeSentToTheWithTheBetaCode($arg1)
    {
        $lastEmail = $this->fetchInbox()[0];
        $betacode = Betacode::where('email', $arg1)->firstOrFail();

        TestCase::assertContains("Please signup kidgifting with following", $lastEmail->html_body);
        TestCase::assertContains("$betacode->betacode", $lastEmail->html_body);
        TestCase::assertContains("Betacode", $lastEmail->subject);
    }

    /**
     * @Then the screen should say Successfully Invited
     */
    public function theScreenShouldSaySuccessfullyInvited()
    {
        //return
    }


}
