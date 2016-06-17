<?php
use App\Child;
use App\User;
use Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount;
use Kidgifting\DwollaWrapper\Models\DwollaSourceAccount;
use Kidgifting\USAlliance\Models\LoanApplication;

/**
 * @author: chengxian
 * Date: 4/16/16
 * @copyright Cheng Xian Lim
 */
class ChildIntegrationTest extends AppBaseTest
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        exec('php artisan migrate --database sqlite_test');
    }

    public static function tearDownAfterClass()
    {
        exec('php artisan migrate:reset --database sqlite_test');
        parent::tearDownAfterClass();
    }

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        Child::truncate();
        LoanApplication::truncate();
        DwollaDestinationAccount::truncate();
        DwollaSourceAccount::truncate();
        User::truncate();

        $this->initFrozenSettings();
    }

    /** @test */
    public function it_is_allowed()
    {
        $c = $this->getAttachedChild();
        $this->assertTrue($c->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_disabled()
    {
        $c = $this->getAttachedChild();
        $c->is_enabled = false;
        $this->assertFalse($c->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_deleted()
    {
        $c = $this->getAttachedChild();
        $c->delete();
        $this->assertFalse($c->isAllowed());
        $this->seeInDatabase('children', ['first_name' => 'Charlie']);
    }

    /** @test */
    public function it_is_not_allowed_because_loan()
    {
        $c = $this->getAttachedChild();
        $relation = $c->loanApplication;
        $relation->is_enabled = false;
        $relation->save();
        $this->assertFalse($c->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_account()
    {
        $c = $this->getAttachedChild();
        $relation = $c->savingsAccount;
        $relation->is_enabled = false;
        $relation->save();
        $this->assertFalse($c->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_parent()
    {
        $c = $this->getAttachedChild();
        $relation = $c->parent;
        $relation->is_enabled = false;
        $relation->save();
        $this->assertFalse($c->isAllowed());
    }

}