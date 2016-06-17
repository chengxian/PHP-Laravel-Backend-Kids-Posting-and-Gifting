<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;


/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Post
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $child_id
 * @property string $title
 * @property string $text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $uuid
 * @property-read mixed $comment_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PostLike[] $likes
 * @property-read mixed $is_like
 * @property-read mixed $diff_from_created
 * @property-read mixed $sort_col
 * @property-read \App\User $user
 * @property-read \App\Child $child
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\FundingContribution[] $gifts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PostAttachment[] $attachments
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereChildId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereUuid($value)
 * @mixin \Eloquent
 * @property-read mixed $sort_col
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post whereIsEnabled($value)
 */
class Post extends Model
{
    /**
     * Hide attributes
     */
    protected $hidden = ['created_at', 'updated_at'];

    /*
     * The accesors to append to the model
     */
    protected $appends = ['comment_count', 'likes', 'is_like', 'diff_from_created', 'sort_col'];

    public static function boot()
    {
        parent::boot();

        /**
         * @param Post $item
         */
        $uuidClosure = function($item) {
            if ($item->uuid == null) {
                $item->uuid = Uuid::generate(4);
            }
        };

        static::saving($uuidClosure);
    }

    /*
     * Get comment count
     */
    public function getCommentCountAttribute(){
        return $this->comments()->count();
    }

    /*
     * Get if user like this post
     */
    public function getLikesAttribute(){
        return $this->likes()->count();
    }

    /*
     * Get if user like this post
     */
    public function getIsLikeAttribute(){
        $likes = $this->likes()->get();
        $user = Auth::user();
        $is_like = false;
        foreach ($likes as $like) {
            if ($like->user_id == $user->id && $like->post_id == $this->attributes['id']) {
                $is_like = true;
            }
        }

        return $is_like;
    }

    /*
     * Get differnce time from created
     */
    public function getDiffFromCreatedAttribute(){
        $now = Carbon::now();
        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at']);

        $diffString = "";
        $diffMinutes = $created_at->diffInMinutes($now);
        if ($diffMinutes < 60) {
            return $diffMinutes . "m";
        }

        $diffHours = $created_at->diffInHours($now);
        if ($diffHours < 24) {
            return $diffHours . "h";
        }

        $diffDays = $created_at->diffInDays($now);
        if ($diffDays < 31) {
            return $diffDays . "d";
        }

        $diffWeeks = $created_at->diffInWeeks($now);
        if ($diffWeeks < 5) {
            return $diffWeeks . "w";
        }

        $diffMonths = $created_at->diffInMonths($now);
        if ($diffMonths < 12) {
            return $diffMonths . "m";
        }

        $diffYears = $created_at->diffInYears($now);
        return $diffYears."y";
    }

    public function getSortColAttribute() {
        return $this->created_at->toDateTimeString();
    }
    
    /** 
     * Get author associated with the post.
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /** 
     * Get child associated with the post.
     */
    public function child(){
        return $this->belongsTo('App\Child', 'child_id');
    }

    /** 
     * Get likes associated with the post.
     */
    public function likes(){
        return $this->hasMany('App\PostLike');
    }

    /** 
     * Get gifts associated with the post.
     */
    public function gifts(){
        return $this->hasMany('App\FundingContribution');
    }

    /** 
     * Get comments associated with the post.
     */
    public function comments(){
        return $this->hasMany('App\Comment');
    }
    
    /** 
     * Get attachments associated with the post.
     */
    public function attachments(){
        // return $this->hasManyThrough('App\Media', 'App\PostAttachment', 'post_id', 'attachment_id');
        return $this->hasMany('App\PostAttachment', 'post_id');
    }
}
