<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Following
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $child_id
 * @property \Carbon\Carbon $created_at
 * @property-read \App\Child $child
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereChildId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereCreatedAt($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereIsEnabled($value)
 * @property-read \App\User $user
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Following whereIsEnabled($value)
 */
class Following extends Model
{
	/**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */

    // TODO why?
    public $timestamps = false;
    
    /** 
     * Get the child associated with the following.
     */
    public function child(){
        return $this->belongsTo('App\Child', 'child_id');
    }

    /**
     * Get the user associated with the following.
     */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
