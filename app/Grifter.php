<?php

namespace App;

use App\Exceptions\GrifterException;
use App\Exceptions\NumberException;
use Kidgifting\DwollaWrapper\DwollaWrapperTransferClient;
use Kidgifting\USAlliance\MustString;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class Grifter
{
    use MustString;

    CONST FEE_PERCENTAGE = 5.0;
    CONST MAX_FEE_PERCENTAGE = 50;
    /*
     *     public function handle(DwollaWrapperTransferClient $transferClient)
    {
        $user = User::whereIsParent(false)->first();
        $child = Child::all()->first();
        $parent = $child->parent;
        $fundable = Fundable::whereFundableType('Kidgifting\DwollaWrapper\Models\DwollaSourceAccount')->first();

        $gift = new FundingContribution;
        $gift->child()->associate($child);
        $gift->fundable_id = $fundable->id;
        $gift->user_id = $parent->id;
        $gift->save();

        $job = (new SendGift($gift))->onQueue('gifts');
        dispatch($job);
    }
     */

    /**
     * @param User $user
     * @param Child $child
     * @param Fundable $fundable
     * @param $amount
     * @param bool $isGift
     * @param null $giftMessage
     * @return FundingContribution
     * @throws NumberException
     */
    public function makeFundingContribution(
        User $user,
        Child $child,
        Fundable $fundable,
        $amount,
        $isGift = false,
        $giftMessage = null
    ) {
        if (!(is_int($amount) || is_double($amount))) {
            throw new NumberException("Amount must be an int or double");
        }

        if ($giftMessage != null) {
            $this->checkParams([$giftMessage]);
        }
        
        $gift = new FundingContribution();
        $fee = 0;
        $feePercent = 0;
        $charityAmount = 0;
        $userIsParent = $this->isParent($user, $child);
        $charityPercentage = $child->parent->setting->donation_percent;

        if ($charityPercentage < 0) {
            $charityPercentage = 0;
        }

        if ($charityPercentage > 100) {
            $charityPercentage = 100;
        }

        /*
         * Need to calculate fee
         * No fees for parents
         */
        if (!$userIsParent) {
            if (self::FEE_PERCENTAGE > 0) {
                $fee = round($amount * (self::FEE_PERCENTAGE * 0.01), 2);
                $feePercent = self::FEE_PERCENTAGE;
            }
        }

        /*
         * total fees can not be over 50%
         * https://developers.dwolla.com/resources/facilitator-fee.html
         */

        $totalFeePercentage = $charityPercentage + $feePercent;
        if ($totalFeePercentage > self::MAX_FEE_PERCENTAGE) {
            $charityPercentage = self::MAX_FEE_PERCENTAGE - $feePercent;
        }

        if ($charityPercentage != null && $charityPercentage > 0) {
            $charityAmount = round($amount * ($charityPercentage * 0.01), 2);
        }

        $gift->child()->associate($child);
        $gift->user()->associate($user);
        $gift->amount = $amount;
        $gift->is_gift = $isGift;
        $gift->gift_message = $giftMessage;
        $gift->fee_percent = $feePercent;
        $gift->fee_amount = $fee;
        $gift->contribution_percent = $charityPercentage;
        $gift->contribution_amount = $charityAmount;
        $gift->fundable_id = $fundable->id;

        return $gift;
    }

    /**
     * @param User $user
     * @param Child $child
     * @return bool
     */
    protected function isParent(User $user, Child $child)
    {
        $parent = $child->parent;

        return ($user->id == $parent->id);
    }

    /**
     * @param FundingContribution $contribution
     * @param DwollaWrapperTransferClient $dwollaClient
     * @return FundingContribution
     * @throws GrifterException
     */
    public function startTransfer(FundingContribution $contribution, DwollaWrapperTransferClient $dwollaClient)
    {
        $fundable = $contribution->fundable;
        if (!$fundable) {
            throw new GrifterException("FundingContribution $contribution->id needs a fundable to transfer");
        }

        $sourceAccount = $fundable->fundable;
        if (!$sourceAccount) {
            throw new GrifterException("FundingContribution $contribution->id needs a source account to transfer");
        }

        $destinationAccount = $contribution->child->savingsAccount;
        if (!$destinationAccount) {
            throw new GrifterException("FundingContribution $contribution->id needs a destination account to transfer");
        }

        $amount = $contribution->amount;
        if (!$amount) {
            throw new GrifterException("FundingContribution $contribution->id needs an amount to transfer");
        }

        $charityPercentage = $contribution->contribution_percent;

        $dwollaTransfer = $dwollaClient->createFfToParentTransfer(
            $sourceAccount->dwolla_id,
            $destinationAccount->dwolla_id,
            $amount,
            $charityPercentage);

        // FIXME validate response
//        $contribution->transfer()->save($dwollaTransfer);
        $dwollaTransfer->fundingContribution()->save($contribution);

        return $contribution;
    }
}
