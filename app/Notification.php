<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Notification
 *
 * @property-read \App\User $user
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $sender_id
 * @property integer $receiver_id
 * @property integer $child_id
 * @property string $type
 * @property string $text
 * @property string $custom_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $sender
 * @property-read \App\User $receiver
 * @property-read \App\Child $child
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereSenderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereReceiverId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereChildId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereCustomData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereUpdatedAt($value)
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Notification whereIsEnabled($value)
 */
class Notification extends Model
{
    /** 
     * sender
     */
    public function sender(){
        return $this->belongsTo('App\User');
    }

    /** 
     * receiver
     */
    public function receiver(){
        return $this->belongsTo('App\User');
    }

    /** 
     * receiver
     */
    public function child(){
        return $this->belongsTo('App\Child');
    }

}
