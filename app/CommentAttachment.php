<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\CommentAttachment
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $attachment_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Comment $comment
 * @property-read \App\Media $media
 * @method static \Illuminate\Database\Query\Builder|\App\CommentAttachment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CommentAttachment whereCommentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CommentAttachment whereAttachmentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CommentAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CommentAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommentAttachment extends Model
{
    /** 
     * Get the comment.
     */
    public function comment(){
        return $this->belongsTo('App\Comment');
    }

    /** 
     * Get the media.
     */
    public function media(){
        return $this->belongsTo('App\Media', 'attachment_id');
    }
}
