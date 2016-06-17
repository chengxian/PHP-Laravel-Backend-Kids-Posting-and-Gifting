<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\PostAttachment
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $attachment_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Post $post
 * @property-read \App\Media $media
 * @method static \Illuminate\Database\Query\Builder|\App\PostAttachment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostAttachment wherePostId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostAttachment whereAttachmentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PostAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PostAttachment extends Model
{
    /**
     * Hide attributes
     */
    protected $hidden = ['id', 'created_at', 'updated_at'];    

    /** 
     * Get the post.
     */
    public function post(){
        return $this->belongsTo('App\Post');
    }

    /** 
     * Get the media.
     */
    public function media(){
        return $this->belongsTo('App\Media', 'attachment_id');
    }
    
}
