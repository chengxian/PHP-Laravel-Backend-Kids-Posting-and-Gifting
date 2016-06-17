<?php

namespace App;

use App\Enums\RecurringContributionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\RecurringContribution
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $child_id
 * @property float $amount
 * @property string $recurring_type
 * @property \Carbon\Carbon $start_date
 * @property integer $fundable_id
 * @property string $checked_at
 * @property boolean $day_of_week
 * @property boolean $day_of_month
 * @property boolean $day_of_year
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property boolean $is_enabled
 * @property-read \App\User $user
 * @property-read \App\Child $child
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Fundable[] $fundable
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereChildId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereRecurringType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereStartDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereFundableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereCheckedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereDayOfWeek($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereDayOfMonth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereDayOfYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RecurringContribution whereIsEnabled($value)
 * @mixin \Eloquent
 */
class RecurringContribution extends Model
{
    use RevisionableTrait;

    protected $table = 'recurring_contributions_schedule';
    protected $dates = ['start_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * update data needed for calculating if a new contribution should be created later on
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $startDate = $this->start_date;
        switch ($this->recurring_type) {
            case RecurringContributionType::WEEKLY:
                $this->day_of_week = $startDate->dayOfWeek;
                break;
            case RecurringContributionType::MONTHLY:
                $this->day_of_month = $startDate->day;

                // leap year
                if ($startDate->month == 1 && $this->day_of_month == 29) {
                    $this->day_of_month == 28;
                }
                break;
            case RecurringContributionType::YEARLY:
                $this->day_of_year = $startDate->dayOfYear;
                break;
        }
        return parent::save($options);
    }

    /**
     * @param Carbon $date
     * @return bool
     */
    public function shouldSpawn(Carbon $date)
    {
        switch ($this->recurring_type) {
            case RecurringContributionType::DAILY:
                return true;
            case RecurringContributionType::WEEKLY:
                if ($this->day_of_week == $date->dayOfWeek) {
                    return true;
                }
                break;
            case RecurringContributionType::MONTHLY:
                // for is same number of days
                if ($this->day_of_month == $date->day) {
                    return true;
                }

                // do on days that go over the number of days in the current month
                if ($this->day_of_month > $date->daysInMonth) {
                    return true;
                }
                break;
            case RecurringContributionType::YEARLY:
                if ($date->isLeapYear()) {
                    if ($date->month <= 1 && $this->start_date->dayOfYear == $date->dayOfYear) {
                        return true;
                    } elseif ($this->start_date->dayOfYear == $date->dayOfYear-1) {
                        return true;
                    }
                } else {
                    if ($this->start_date->dayOfYear == $date->dayOfYear) {
                        return true;
                    }

                }
                break;
        }
        return false;
    }

    /**
     * Copy a Recurring Contribution to a Funding Contribution
     * @return FundingContribution
     */
    public
    function spawnToFundingContribution()
    {
        $contribution = new FundingContribution();
        $contribution->user_id = $this->user_id;
        $contribution->child_id = $this->child_id;
        $contribution->amount = $this->amount;
        $contribution->fundable_id = $this->fundable_id;

        // FIXME calculate the fees depending on where @Yan set it up
        return $contribution;
    }

    /**
     * Get the user associated with the contribution.
     */
    public
    function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the child associated with the contribution.
     */
    public
    function child()
    {
        return $this->belongsTo('App\Child', 'child_id');
    }

    /**
     * Get the funding account associated with the contribution.
     */
    public
    function fundable()
    {
        return $this->hasMany('App\Fundable');
    }
}
