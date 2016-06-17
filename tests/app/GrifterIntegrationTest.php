<?php
use App\Child;
use App\Fundable;
use App\FundingContribution;
use App\Setting;
use App\User;
use Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount;
use Kidgifting\DwollaWrapper\Models\DwollaSourceAccount;
use Kidgifting\DwollaWrapper\Models\DwollaTransfer;
use Kidgifting\FrozenSettings\FrozenSetting;
use Kidgifting\USAlliance\Models\LoanApplication;

/**
 * @author: chengxian
 * Date: 4/16/16
 * @copyright Cheng Xian Lim
 */
class GrifterIntegrationTest extends AppBaseTest
{
    /** @var \App\Child */
    protected $child;

    /** @var  \App\User */
    protected $user;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        exec('php artisan migrate:reset --database sqlite_test');
        exec('php artisan migrate --database sqlite_test');
    }

    public static function tearDownAfterClass()
    {
//        exec('php artisan migrate:reset --database sqlite_test');
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
        DwollaTransfer::truncate();
        User::truncate();
        FundingContribution::truncate();
        Setting::truncate();
        Fundable::truncate();

        $this->child = $this->getAttachedChild();
        $this->user = $this->getAttachedUser();

        $this->child->parent_id = $this->user->id;
        $this->child->save();
    }


    public function getMockDwollaTransferClient()
    {
        $mock = parent::getMockDwollaTransferClient();
        $transfer = $this->getRealDwollaTransfer(101.00, 5.05, 12.00);
        $transfer->save();
        $mock->method('createFfToParentTransfer')->willReturn($transfer);

        return $mock;
    }

    public function getRealFundingContributionFromGrifter()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 12.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 101.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

        return $contribution;
    }

    /** @test */
    public function it_creates_parent_contribution_no_charity()
    {
        $setting = $this->user->setting;
        $setting->donation_percent = 0.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 100.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($this->user, $this->child, $fundable, $amount);

        $this->assertEquals(100.00, $contribution->amount);
        $this->assertEquals(0.00, $contribution->contribution_amount);
        $this->assertEquals(0.00, $contribution->contribution_percent);
        $this->assertEquals(0.00, $contribution->fee_amount);
        $this->assertEquals(0.00, $contribution->fee_percent);
    }

    /** @test */
    public function it_creates_parent_contribution_with_charity()
    {
        $setting = $this->user->setting;
        $setting->donation_percent = 12.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 101.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($this->user, $this->child, $fundable, $amount);

        $this->assertEquals(101.00, $contribution->amount);
        $this->assertEquals(12.12, $contribution->contribution_amount);
        $this->assertEquals(12.00, $contribution->contribution_percent);
        $this->assertEquals(0.00, $contribution->fee_amount);
        $this->assertEquals(0.00, $contribution->fee_percent);
    }

    /** @test */
    public function it_creates_ff_contribution_no_charity()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 0.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 101.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

        $this->assertEquals(101.00, $contribution->amount);
        $this->assertEquals(0.00, $contribution->contribution_amount);
        $this->assertEquals(0.00, $contribution->contribution_percent);
        $this->assertEquals(5.05, $contribution->fee_amount);
        $this->assertEquals(5.00, $contribution->fee_percent);
    }

    /** @test */
    public function it_creates_ff_contribution_with_charity()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 12.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 101.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

        $this->assertEquals(101.00, $contribution->amount);
        $this->assertEquals(12.12, $contribution->contribution_amount);
        $this->assertEquals(12.00, $contribution->contribution_percent);
        $this->assertEquals(5.05, $contribution->fee_amount);
        $this->assertEquals(5.00, $contribution->fee_percent);
    }

    /** @test */
    public function it_creates_ff_contribution_with_negative_charity()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = -12.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 101.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

        $this->assertEquals(101.00, $contribution->amount);
        $this->assertEquals(0.00, $contribution->contribution_amount);
        $this->assertEquals(0.00, $contribution->contribution_percent);
        $this->assertEquals(5.05, $contribution->fee_amount);
        $this->assertEquals(5.00, $contribution->fee_percent);
    }

    /** @test */
    public function it_creates_ff_contribution_with_too_high_charity()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 120.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 100.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

        $this->assertEquals(100.00, $contribution->amount);
        $this->assertEquals(45.00, $contribution->contribution_amount);
        $this->assertEquals(45.00, $contribution->contribution_percent);
        $this->assertEquals(5.00, $contribution->fee_amount);
        $this->assertEquals(5.00, $contribution->fee_percent);
    }

    /** @test */
    public function it_creates_ff_contribution_with_too_high_charity2()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 120.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 101.00;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

        $this->assertEquals(101.00, $contribution->amount);
        $this->assertEquals(45.45, $contribution->contribution_amount);
        $this->assertEquals(45.00, $contribution->contribution_percent);
        $this->assertEquals(5.05, $contribution->fee_amount);
        $this->assertEquals(5.00, $contribution->fee_percent);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\NumberException
     */
    public function it_does_not_create_contribution_bad_amount()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 120.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = "100";
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount);

    }

    /**
     * @test
     * @expectedException \Kidgifting\USAlliance\StringException
     */
    public function it_does_not_create_contribution_bad_giftmessage()
    {
        $ff = $this->getFFUser();
        $setting = $this->user->setting;
        $setting->donation_percent = 120.00;
        $setting->save();

        $fundable = $this->user->fundable->first();
        $amount = 100;
        $grifter = $this->getRealGrifter();
        $contribution = $grifter->makeFundingContribution($ff, $this->child, $fundable, $amount, true, ['hi']);

    }

    /** @test */
    public function it_starts_a_dwolla_transfer()
    {
        $grifter = $this->getRealGrifter();
        $gift = $this->getRealFundingContributionFromGrifter();
        $gift->save();
        $client = $this->getMockDwollaTransferClient();

        $gift = $grifter->startTransfer($gift, $client);

        $transfer = $gift->transfer;

        $giftId = $gift->id;
        $transferId = $transfer->id;

        $newGift = FundingContribution::whereId($giftId)->firstOrFail();
        $newTransfer = DwollaTransfer::whereId($transferId)->firstOrFail();
        $this->assertEquals($gift->amount, $transfer->amount);
        $this->assertEquals($newGift->transfers_id, $newTransfer->id);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\GrifterException
     * @expectedExceptionMessage needs a fundable to transfer
     */
    public function it_does_not_start_a_dwolla_transfer_because_missing_fundable()
    {
        $grifter = $this->getRealGrifter();
        $gift = $this->getRealFundingContributionFromGrifter();
        $gift->save();
        $client = $this->getMockDwollaTransferClient();

        $gift->fundable_id = null;
        $gift->save();

        $gift = $grifter->startTransfer($gift, $client);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\GrifterException
     * @expectedExceptionMessage needs a source account to transfer
     */
    public function it_does_not_start_a_dwolla_transfer_because_missing_source_account()
    {
        $grifter = $this->getRealGrifter();
        $gift = $this->getRealFundingContributionFromGrifter();
        $gift->save();
        $client = $this->getMockDwollaTransferClient();

        $fundable = $gift->fundable;
        $fundable->fundable_id = 500;
        $fundable->fundable_type = 'App\User';
        $fundable->save();

        $gift = $grifter->startTransfer($gift, $client);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\GrifterException
     * @expectedExceptionMessage needs a destination account to transfer
     */
    public function it_does_not_start_a_dwolla_transfer_because_missing_destination_account()
    {
        $grifter = $this->getRealGrifter();
        $gift = $this->getRealFundingContributionFromGrifter();
        $gift->save();
        $client = $this->getMockDwollaTransferClient();

        $child = $gift->child;
        $child->dwolla_destination_account_id = null;
        $child->save();

        $gift = $grifter->startTransfer($gift, $client);
    }

    /**
     * @test
     * @expectedException \App\Exceptions\GrifterException
     * @expectedExceptionMessage needs an amount to transfer
     */
    public function it_does_not_start_a_dwolla_transfer_because_missing_amount()
    {
        $grifter = $this->getRealGrifter();
        $gift = $this->getRealFundingContributionFromGrifter();
        $gift->save();
        $client = $this->getMockDwollaTransferClient();

        $gift->amount = null;

        $gift = $grifter->startTransfer($gift, $client);
    }
}