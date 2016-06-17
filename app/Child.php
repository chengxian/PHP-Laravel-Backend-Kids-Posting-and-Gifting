<?php

namespace App;

use App\Exceptions\DeletedException;
use App\Exceptions\NotEnabledException;
use App\Exceptions\ParentNotAllowedException;
use App\Interfaces\Allowable;
use App\Traits\Allowed;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kidgifting\USAlliance\Models\LoanApplication;
use Kidgifting\USAlliance\Models\Balance;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Child
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $first_name
 * @property string $last_name
 * @property \Carbon\Carbon $birthday
 * @property string $wants
 * @property integer $avatar_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property boolean $fundable
 * @property integer $usa_loan_application_id
 * @property integer $dwolla_destination_account_id
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @property-read mixed $age
 * @property-read \App\User $parent
 * @property-read \App\Media $avatar
 * @property-read \Kidgifting\USAlliance\Models\LoanApplication $loanApplication
 * @property-read \Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount $savingsAccount
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereBirthday($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereWants($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereAvatarId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereFundable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereUsaLoanApplicationId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereDwollaDestinationAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Child whereIsEnabled($value)
 * @mixin \Eloquent
 */
class Child extends Model implements Allowable
{
    use RevisionableTrait;
    use Allowed;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'children';

    /**
     * The attributes that should be mutated to date.
     */
    protected $dates = ['birthday', 'created_at', 'updated_at'];

    /**
     * Hide attributes
     */
    protected $hidden = ['created_at', 'updated_at'];

    /*
     * The accesors to append to the model
     */
    protected $appends = ['age'];

    protected $attributes = [
        'is_enabled' => true
    ];


    /*
     * Get age of child
     */
    public function getAgeAttribute()
    {
        $now = Carbon::now();
        $birthday = Carbon::createFromFormat('Y-m-d', $this->attributes['birthday']);
        return $birthday->diffInYears($now);
    }

    /*
     * Get birthday Mutator
     */
    public function getBirthdayAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d');
    }

    /**
     *    Get the parent that owns the child.
     */
    public function parent()
    {
        return $this->belongsTo('App\User', 'parent_id');
    }

    /**
     *    Get the media of child avatar.
     */
    public function avatar()
    {
        return $this->belongsTo('App\Media', 'avatar_id');
    }

    /**
     * Get US.A Loan Application
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loanApplication()
    {
        return $this->belongsTo('Kidgifting\USAlliance\Models\LoanApplication', 'usa_loan_application_id');
    }

    /**
     * Get Dwolla Account that points to US.A Savings account
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function savingsAccount()
    {
        return $this->belongsTo('Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount', 'dwolla_destination_account_id');
    }

    /**
     * @return bool
     */
    public function isFundable()
    {
        $loanApplication = $this->loanApplication()->getRelated();
        if ($loanApplication != null
            && in_array($loanApplication->status, LoanApplication::$positiveApprovals)
        ) {
            $savingsAccount = $this->savingsAccount()->getRelated();
            if ($savingsAccount != null
                && $savingsAccount->dwolla_id != null
            ) {
                return true;
            }
        }
        return false;
    }

    public function getFullNameAttribute()
    {
        $firstName = $this->first_name;
        $lastName = $this->last_name;
        return "$firstName $lastName";
    }

    public function parentIsAllowedToQueueTransfer()
    {
        $parentAllowed = $this->parent->isAllowed();
        $loanExists = ($this->usa_loan_application_id != null && $this->usa_loan_application_id > 0);
        $loanNotApproved = !$this->loanApplication->isApproved();

        return ($parentAllowed && $loanExists && $loanNotApproved);
    }

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

        $loanAllowed = $this->loanApplication->isAllowed();
        if (!$loanAllowed) {
            $this->addDisallowedExceptions($this->loanApplication->getDisallowedExceptions());
        }

        $dwollaAllowed = $this->savingsAccount->isAllowed();
        if (!$dwollaAllowed) {
            $this->addDisallowedExceptions($this->savingsAccount->getDisallowedExceptions());
        }
        
        $parentAllowed = $this->parent->isAllowed();
        if (!$parentAllowed) {
            $this->addDisallowedException(new ParentNotAllowedException());
            $this->addDisallowedExceptions($this->parent->getDisallowedExceptions());
        }

        return ($enabled && $notDeleted && $loanAllowed && $dwollaAllowed && $parentAllowed);
    }
}
