<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\PostLike
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $user_id
 * @property string $created_at
 * @property-read \App\Post $post
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\PostLike whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostLike wherePostId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostLike whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostLike whereCreatedAt($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\PostLike whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostLike whereIsEnabled($value)
 */
class PostLike extends Model
{
	/**
     * Hide attributes
     */
    protected $hidden = ['created_at'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /** 
     * Get post associated with the like.
     */
    public function post(){
        return $this->belongsTo('App\Post');
    }

	/** 
     * Get user associated with the like.
     */
    public function user(){
        return $this->belongsTo('App\User');
    }    
}
