<?php

namespace App;

use App\Exceptions\DeletedException;
use App\Exceptions\NotEnabledException;
use App\Interfaces\Allowable;
use App\Traits\Allowed;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kidgifting\Emailage\Emailage;
use Kidgifting\LaraVault\LaraVault;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\User
 *
 * @property integer $id
 * @property string $facebook_id
 * @property string $twitter_id
 * @property string $instagram_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $street
 * @property string $street1
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $postcode
 * @property integer $avatar_id
 * @property boolean $is_parent
 * @property boolean $is_admin
 * @property boolean $email_verified
 * @property boolean $accepted_kf_toc
 * @property string $accepted_kf_toc_at
 * @property boolean $full_user
 * @property boolean $status
 * @property boolean $emailage_validated
 * @property boolean $emailage_score
 * @property boolean $emailage_band
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $dob
 * @property integer $dwolla_customer_id
 * @property-read \App\Media $avatar
 * @property-read \App\Media $idcard
 * @property-read \App\Setting $setting
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Child[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Fundable[] $fundable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\FundingContribution[] $fundingcontributions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Following[] $followings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invite[] $invites
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invite[] $invitesFrom
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Notification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Device[] $devices
 * @property-read \Kidgifting\DwollaWrapper\Models\DwollaUnerifiedCustomer $dwollaCustomer
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFacebookId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereTwitterId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereInstagramId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStreet($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStreet1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePostcode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAvatarId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsParent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAcceptedKfToc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAcceptedKfTocAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFullUser($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmailageValidated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmailageScore($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmailageBand($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDwollaCustomerId($value)
 * @mixin \Eloquent
 * w0
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsEnabled($value)
 */
class User extends Authenticatable implements Allowable
{
    use LaraVault;
    use Emailage;
    use RevisionableTrait;
    use Allowed;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'avatar_id', 'facebook_id', 'instagram_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'status', 'created_at', 'updated_at',
    ];

    protected $attributes = [
        'is_enabled' => true
    ];

    /**
     * LaraVault
     * @var array
     */
    protected $encrypts = [
        'phone',
        'street',
        'street1',
        'city',
        'state',
        'country',
        'postcode',
        'dob',
    ];

    /**
     * RevisionableTrait
     * @var array
     */
    protected $dontKeepRevisionOf = [
        'phone',
        'street',
        'street1',
        'city',
        'state',
        'country',
        'postcode',
        'dob',
    ];

    protected $dates = ['deleted_at'];

    /** 
     * Get the setting record associated with the user.
     */
    public function avatar(){
        return $this->belongsTo('App\Media', 'avatar_id');
    }

    /** 
     * Get the setting record associated with the user.
     */
    public function idcard(){
        return $this->belongsTo('App\Media', 'idcard_id');
    }

    /** 
     * Get the avatar record associated with the user.
     */
    public function setting(){
        return $this->hasOne('App\Setting');
    }

    /** 
     * Get children of the user.
     */
    public function children(){
        return $this->hasMany('App\Child', 'parent_id');
    }

    /** 
     * Get funding accounts associated with the user.
     */
    /*public function fundingaccounts(){
        return $this->belongsToMany('Kidgifting\DwollaWrapper\Models\DwollaSourceAccount', 'dwolla_account_user', 'dwolla_account_id', 'user_id')
            ->whereType('source');

    }*/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fundable() {
        return $this->hasMany('App\Fundable');
    }

    /**
     * Get source DwollaSourceAccount collection
     * @return DwollaSourceAccount collection
     */
    public function fundingAccounts() {
        return $this->morphedByMany('Kidgifting\DwollaWrapper\Models\DwollaSourceAccount', 'fundable')
            ->whereType('source');
    }

    /**
     * Get source DwollaSourceAccount collection
     * @return DwollaSourceAccount
     */
    public function fundingAccount() {
        return $this->fundingAccounts()->whereType('source')->first();
    }

    /* public function creditCards() {
        return $this->morphedByMany('App\CC', 'fundable');
    }*/

    /** 
     * Get funding contributions associated with the user.
     */
    public function fundingcontributions(){
        return $this->hasMany('App\FundingContribution');
    }

    /** 
     * Get followings associated with the user.
     */
    public function followings(){
        return $this->hasMany('App\Following', 'user_id');
    }

    /** 
     * Get invites associated with the user.
     */
    public function invites(){
        return $this->hasMany('App\Invite', 'from_user_id');
    }

    /** 
     * Get invites associated with the user.
     */
    public function invitesFrom(){
        return $this->hasMany('App\Invite', 'to_user_id');
    }

    /** 
     * Get posts associated with the user.
     */
    public function posts(){
        return $this->hasMany('App\Post');
    }    

    /** 
     * Get comments associated with the user.
     */
    public function comments(){
        return $this->hasMany('App\Comment');
    }

    /** 
     * Get notifications that the user received
     */
    public function notifications(){
        return $this->hasMany('App\Notification');
    }

    /** 
     * Get devices that the user is using
     */
    public function devices(){
        return $this->hasMany('App\Device');
    }

    /** 
     * Check if the user is admin
     */
    public function isAdmin() {
        return ($this->attributes['is_admin']) ? true : false;
    }

    private function getDwollaCustomerInstance() {
        return $this->belongsTo('Kidgifting\DwollaWrapper\Models\DwollaVerifiedCustomer', 'dwolla_customer_id')->getRelated()->get()->first();
    }

    /**
     * @return mixed
     */
    public function concreteVerifiedDwollaCustomer(){
        $instance = $this->getDwollaCustomerInstance();
        if ($instance != null && $instance->type = 'verified') {
            return $instance;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function concreteUnverifiedDwollaCustomer(){
        $instance = $this->getDwollaCustomerInstance();
        if ($instance != null && $instance->type = 'unverified') {
            return $instance;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function dwollaCustomer() {
        if ($this->is_parent) {
            return $this->belongsTo('Kidgifting\DwollaWrapper\Models\DwollaVerifiedCustomer', 'dwolla_customer_id')
                ->whereType('verified');
        } else {
            return $this->belongsTo('Kidgifting\DwollaWrapper\Models\DwollaUnerifiedCustomer', 'dwolla_customer_id')
                ->whereType('unverified');
        }
    }

    public function isAllowed()
    {
        $this->countAllowedChecks++;
        
        $enabled = $this->is_enabled;
        $notDeleted = !$this->trashed();

        $enabled = $this->is_enabled;
        if (!$enabled) {
            $this->addDisallowedException(new NotEnabledException("User is disabled"));
        }

        $notDeleted = !$this->trashed();
        if (!$notDeleted) {
            $this->addDisallowedException(new DeletedException("User is deleted"));
        }

        return ($enabled && $notDeleted);
    }


}
