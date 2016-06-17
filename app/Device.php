<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Device
 *
 * @property integer $id
 * @property string $device_token
 * @property integer $badge
 * @property string $channel
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereDeviceToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereBadge($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereChannel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereIsEnabled($value)
 */
class Device extends Model
{
	/**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /** 
     * Get the user record associated with the device.
     */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
