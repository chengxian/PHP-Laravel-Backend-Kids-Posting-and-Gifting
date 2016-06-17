<?php
use App\Child;
use App\FundingContribution;
use App\User;
use Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount;
use Kidgifting\DwollaWrapper\Models\DwollaSourceAccount;
use Kidgifting\USAlliance\Models\LoanApplication;

/**
 * @author: chengxian
 * Date: 4/16/16
 * @copyright Cheng Xian Lim
 */
class FundingContributionIntegrationTest extends AppBaseTest
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
        FundingContribution::truncate();

        $this->initFrozenSettings();
    }

    /** @test */
    public function it_is_allowed()
    {
        $f = $this->getRealFundingContribution();
        $this->assertTrue($f->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_disabled()
    {
        $f = $this->getRealFundingContribution();
        $f->is_enabled = false;
        $this->assertFalse($f->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_deleted()
    {
        $f = $this->getRealFundingContribution();
        $f->save();
        $f->delete();
        $this->assertFalse($f->isAllowed());
        $this->seeInDatabase('funding_contributions', ['amount' => '100']);
    }

    /** @test */
    public function it_is_not_allowed_because_from_user()
    {
        $f = $this->getRealFundingContribution();
        $relation = $f->user;
        $relation->is_enabled = false;
        $relation->save();

        $this->assertFalse($f->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_child()
    {
        $f = $this->getRealFundingContribution();
        $relation = $f->child;
        $relation->is_enabled = false;
        $relation->save();

        $this->assertFalse($f->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_not_approved_needs_it()
    {
        $f = $this->getRealFundingContribution();
        $f->kf_approved = false;
        $f->save();

        $this->assertFalse($f->isAllowed());
    }

    /** @test */
    public function it_allowed_because_kf_approval_not_needed()
    {
        $f = $this->getRealFundingContribution();
        $f->kf_approved = false;
        $f->save();

        $this->setFrozenSetting(self::TRANSFER_SETTING, false);
        $this->assertTrue($f->isAllowed());
    }
}