<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Comment
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $post_id
 * @property integer $parent_id
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $diff_from_created
 * @property-read \App\User $user
 * @property-read \App\Comment $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PostAttachment[] $attachments
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment wherePostId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $is_gift
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereIsGift($value)
 * @property string $uuid
 * @property-read mixed $sort_col
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUuid($value)
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereIsEnabled($value)
 * @property-read \App\Post $post
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereIsEnabled($value)
 */
class Comment extends Model
{
    /**
     * Hide attributes
     */
    protected $hidden = ['created_at', 'updated_at'];

    /*
     * The accesors to append to the model
     */
    protected $appends = ['sort_col'];

    /**
     * @return string
     */
    public function getSortColAttribute() {
        return $this->created_at->toDateTimeString();
    }

    /** 
     * Get author associated with the comment.
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /** 
     * Get child comments associated with the comment.
     */
    public function parent(){
        return $this->belongsTo('App\Comment', 'parent_id');
    }

    /** 
     * Get comments associated with the comment.
     */
    public function children(){
        return $this->hasMany('App\Comment', 'parent_id');
    }
    
    /** 
     * Get attachments associated with the comment.
     */
    public function attachments(){
        return $this->hasMany('App\PostAttachment', 'attachment_id');
    }

    public function post() {
        return $this->belongsTo('App\Post');
    }
}
