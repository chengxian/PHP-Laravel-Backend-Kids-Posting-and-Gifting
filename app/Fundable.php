<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Fundable
 *
 * @property integer $id
 * @property integer $fundable_id
 * @property string $fundable_type
 * @property integer $user_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $fundable
 * @method static \Illuminate\Database\Query\Builder|\App\Fundable whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Fundable whereFundableId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Fundable whereFundableType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Fundable whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\User $user
 */
class Fundable extends Model
{
    protected $table = 'fundables';

    /**
     * Get the fundable object (Dwolla Funding Source)
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fundable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user associated with the following.
     */
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
