<?php
use App\Child;
use App\FundingContribution;
use App\Grifter;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount;
use Kidgifting\DwollaWrapper\Models\DwollaSourceAccount;
use Kidgifting\DwollaWrapper\Models\DwollaTransfer;
use Kidgifting\FrozenSettings\FrozenSetting;
use Kidgifting\USAlliance\Models\LoanApplication;

/**
 * @author: chengxian
 * Date: 5/5/16
 * @copyright Cheng Xian Lim
 */
class AppBaseTest extends TestCase
{
    /**
     *
     */
    public function initFrozenSettings()
    {
        $keys = [self::LOAN_SETTING, self::TRANSFER_SETTING];

        foreach ($keys as $key) {
            $setting = FrozenSetting::firstOrNew(['key' => $key]);
            $setting->value = true;
            $setting->save();
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setFrozenSetting($key, $value)
    {
        $setting = FrozenSetting::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();
    }

    /**
     * @return User
     */
    public function getRealUser()
    {
        $u = User::firstOrNew([
            'first_name' => 'Tim',
            'last_name' => 'Broder',
            'email' => 'john.dae@gmail.com'
        ]);

        return $u;
    }

    /**
     * @return User
     */
    public function getAttachedUser()
    {
        /** @var User */
        $u = User::firstOrNew([
            'first_name' => 'Tim',
            'last_name' => 'Broder',
            'email' => 'administrator@kidgifting.com'
        ]);
        $u->save();
        $sourceAccount = $this->getRealSourceAccount();

        $u->fundingAccounts()->save($sourceAccount);

            $setting = new Setting();
            $setting->user()->associate($u);
            $setting->save();

        return $u;
    }

    public function getFFUser()
    {
        /** @var User */
        $u = User::firstOrNew([
            'first_name' => 'Laura',
            'last_name' => 'Bailyn',
            'email' => 'laura@kidgifting.com'
        ]);
        $u->save();
        $sourceAccount = DwollaSourceAccount::all()->first();

        $u->fundingAccounts()->save($sourceAccount);

        $setting = new Setting();
        $setting->user()->associate($u);
        $setting->save();

        return $u;
    }

    /**
     * @return Child
     */
    public function getRealChild()
    {
        $c = Child::firstOrNew([
            'first_name' => 'Charlie',
            'last_name' => 'Broder'
        ]);
        $c->birthday = new Carbon('July 2, 2015');
        $c->wants = 'Developer';

        return $c;
    }

    /**
     * @return Child
     */
    public function getAttachedChild()
    {
        $c = $this->getRealChild();
        $user = $this->getRealUser();
        $user->save();
        $c->parent_id = $user->id;
        $c->save();
        $loan = $this->getRealLoan();
        $loan->save();
        $loan->child()->save($c);


        $destinationAccount = $this->getRealDestinationAccount();
        $destinationAccount->save();
        $destinationAccount->child()->save($c);

        return $c;
    }

    /**
     * @param User $user
     * @return Setting
     */
    public function getRealSetting(User $user)
    {
        $setting = $user->setting;
        if ($setting == null) {
            $setting = new Setting();
            $setting->user_id = $user->id;
            $setting->save();
        }

        return $setting;
    }

    /**
     * @return FundingContribution
     */
    public function getRealFundingContribution()
    {
        $f = new FundingContribution();
        $c = $this->getAttachedChild();
        $f->child_id = $c->id;
        $f->user_id = $c->parent_id;
        $f->amount = 100;
        $f->is_enabled = true;
        $f->status = 'queued';
        $f->kf_approved = true;

        return $f;
    }

    public function getMockDwollaTransferClient()
    {
        $mock = $this->getMockBuilder('Kidgifting\DwollaWrapper\DwollaWrapperTransferClient')
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    /**
     * @param $amount
     * @param $fee
     * @param $donation
     * @return DwollaTransfer
     */
    public function getRealDwollaTransfer($amount, $fee, $donation)
    {
        $mock = new DwollaTransfer();
        $mock->dwolla_id = '123456';
        $mock->dwolla_id_hashed = 'abcdefg';
        $mock->amount = $amount;
        $mock->facilitator_fee_amount = $fee;
        $mock->charity_amount = $donation;

        return $mock;
    }

    public function getMockLoan()
    {
        /** @var \Kidgifting\USAlliance\Models\LoanApplication $object */
        $mock = $this->getMockBuilder('Kidgifting\USAlliance\Models\LoanApplication')
            ->getMock();

        $mock->method('isAvailable')->willReturn(true);

        $mock->loan_id = '12345';
        $mock->loan_num = 'abcdefg';

        return $mock;
    }

    /**
     * @return LoanApplication
     */
    public function getRealLoan()
    {
        $loan = new LoanApplication();
        $loan->loan_id = '123';
        $loan->loan_id_hashed = '123';
        $loan->loan_number = 'abc';
        $loan->loan_number_hashed = 'abc';
        $loan->status = 'APPROVED';
        $loan->kf_approved = true;
        return $loan;
    }

    /**
     * @return DwollaDestinationAccount
     */
    public function getRealDestinationAccount()
    {
        $account = new DwollaDestinationAccount();
        $account->type = 'destination';
        $account->status = 'UNVERIFIED';

        $account->dwolla_id = '12345';
        $account->dwolla_id_hashed = 'dadasdasda';

        return $account;
    }

    /**
     * @return DwollaSourceAccount
     */
    public function getRealSourceAccount()
    {
        $account = new DwollaSourceAccount();
        $account->type = 'source';
        $account->status = 'verified';

        $account->dwolla_id = '678910';
        $account->dwolla_id_hashed = 'lklklklklklk';

        return $account;
    }

    /**
     * @return Grifter
     */
    public function getRealGrifter()
    {
        return new Grifter();
    }

    /** @test */
    public function this_supresses_a_no_test_warning()
    {
    }
}