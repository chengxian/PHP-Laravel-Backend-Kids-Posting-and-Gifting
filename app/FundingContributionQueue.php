<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\FundingContributionQueue
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $child_id
 * @property float $amount
 * @property boolean $is_gift
 * @property string $gift_message
 * @property float $fee_percent
 * @property float $fee_amount
 * @property float $contribution_percent
 * @property float $contribution_amount
 * @property integer $fundable_id
 * @property boolean $is_enabled
 * @property boolean $kf_approved
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \App\User $user
 * @property-read \App\Child $child
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereChildId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereIsGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereGiftMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereFeePercent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereFeeAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereContributionPercent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereContributionAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereFundableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereIsEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereKfApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FundingContributionQueue whereDeletedAt($value)
 * @mixin \Eloquent
 */
class FundingContributionQueue extends Model
{
    use SoftDeletes;
    protected $table = 'funding_contribution_queue';

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
     * We can do a gift now! lets create one
     * @return FundingContribution
     * @throws Exception
     */
    public function spawnToFundingContribution()
    {
        if ($this->user_id != $this->child->parent_id) {
            throw new Exception("User must be the parent of the child");
        }

        $gift = new FundingContribution();
        $gift->user_id = $this->user_id;
        $gift->child_id = $this->child_id;
        $gift->amount = $this->amount;
        $gift->is_gift = $this->is_gift;
        $gift->gift_message = $this->gift_message;
        $gift->fee_percent = $this->fee_percent;
        $gift->fee_amount = $this->fee_amount;
        $gift->contribution_percent = $this->contribution_percent;
        $gift->contribution_amount = $this->contribution_amount;
        $gift->fundable_id = $this->fundable_id;

        return $gift;
    }

}
