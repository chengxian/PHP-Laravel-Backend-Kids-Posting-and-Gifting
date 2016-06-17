<?php

namespace App;

use App\Exceptions\DeletedException;
use App\Exceptions\LoanNotApprovedException;
use App\Exceptions\NotEnabledException;
use App\Interfaces\Allowable;
use App\Traits\Allowed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kidgifting\FrozenSettings\FrozenSetting;
use Venturecraft\Revisionable\RevisionableTrait;


/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\FundingContribution
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $child_id
 * @property float $amount
 * @property boolean $is_gift
 * @property string $gift_message
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property float $fee_percent
 * @property float $fee_amount
 * @property float $contribution_percent
 * @property float $contribution_amount
 * @property integer $fundable_id
 * @property string $status
 * @property integer $transfers_id
 * @property string $transfers_type
 * @property-read \App\User $user
 * @property-read \App\Child $child
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Fundable[] $fundable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $transfer
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereChildId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereIsGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereGiftMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereFeePercent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereFeeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereContributionPercent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereContributionAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereFundableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereTransfersId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereTransfersType($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @property boolean $kf_approved
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereIsEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContribution whereKfApproved($value)
 */
class FundingContribution extends Model implements Allowable
{
    use RevisionableTrait;
    use Allowed;
    use SoftDeletes;

    protected $attributes = [
        'is_enabled' => true
    ];


    /** 
     * Get the user associated with the contribution.
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /** 
     * Get the child associated with the contribution.
     */
    public function child(){
        return $this->belongsTo('App\Child', 'child_id');
    }

    /** 
     * Get the funding account associated with the contribution.
     */
    public function fundable() {
        return $this->belongsTo('App\Fundable', 'fundable_id');
    }

    /**
     * @return DwollaSourceAccount collection
     */
    public function fundingAccounts() {
        return $this->morphedByMany('Kidgifting\DwollaWrapper\Models\DwollaSourceAccount', 'fundable')
            ->whereType('source');
    }

    /**
     * @return concrete DwollaSourceAccount
     */
    public function fundingAccount() {
        return $this->fundingAccounts()->whereType('source')->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function transfer()
    {
        return $this->morphTo('transfers');
    }

    public function isParentAllowedToQueue()
    {
        $isParent = ($this->user_id == $this->child->parent_id);
        $loanCanQueue = $this->child->loanApplication->canQueueTo();

        return ($isParent && $loanCanQueue);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isAllowed()
    {
        $this->countAllowedChecks++;

        $enabled = $this->is_enabled;
        if (!$enabled) {
            $this->addDisallowedException(new NotEnabledException("Child is disabled"));
        }

        $notDeleted = !$this->trashed();
        if (!$notDeleted) {
            $this->addDisallowedException(new DeletedException("Child is deleted"));
        }
        
        $fromUserAllowed = $this->user->isAllowed();
        if (!$fromUserAllowed) {
            $this->addDisallowedExceptions($this->user->getDisallowedExceptions());
        }

        $childAllowed = $this->child->isAllowed();
        if (!$childAllowed) {
            $this->addDisallowedExceptions($this->child->getDisallowedExceptions());
        }

        $kfNeedsApprove = FrozenSetting::forKey('kf_transaction_approval_required');
        $kfApproved = (!$kfNeedsApprove || ($kfNeedsApprove && $this->kf_approved));
        if (!$kfApproved) {
            $this->addDisallowedException(new LoanNotApprovedException("Loan is in Kidgifting Approval Queue"));
        }

        return ($enabled && $notDeleted && $fromUserAllowed && $childAllowed && $kfApproved);
    }

    /**
     * We can't give a gift right now, apparently
     * Let's prep the data to queue
     * @return FundingContributionQueue
     */
    public function spawnToFundingContributionQueue()
    {
        $queued = new FundingContributionQueue();
        $queued->user_id = $this->user_id;
        $queued->child_id = $this->child_id;
        $queued->amount = $this->amount;
        $queued->is_gift = $this->is_gift;
        $queued->gift_message = $this->gift_message;
        $queued->fee_percent = $this->fee_percent;
        $queued->fee_amount = $this->fee_amount;
        $queued->contribution_percent = $this->contribution_percent;
        $queued->contribution_amount = $this->contribution_amount;
        $queued->fundable_id = $this->fundable_id;

        return $queued;
    }
}
