<?php

namespace App;

use App\Traits\UniqueCode;
use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Invite
 *
 * @property integer $id
 * @property integer $from_user_id
 * @property integer $to_user_id
 * @property string $email
 * @property string $invite_code
 * @property boolean $accepted
 * @property string $created_at
 * @property string $updated_at
 * @property-read \App\User $fromUser
 * @property-read \App\User $toUser
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereFromUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereToUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereInviteCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereAccepted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereIsEnabled($value)
 */
class Invite extends Model
{
    use UniqueCode;
    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    public function generateCode()
    {
        return $this->generateCodeForCol('invite_code');
    }

    /**
     *	Get the users that invited.
     */
    public function fromUser(){
    	return $this->belongsTo('App\User', 'from_user_id');
    }

    /**
     *	Get the users that are invited.
     */
    public function toUser(){
    	return $this->belongsTo('App\User', 'to_user_id');
    }

    /**
     *	Check if the invite is accepted
     */
    public function isAccepted(){
    	return $this->attributes['accepted'];
    }
}
