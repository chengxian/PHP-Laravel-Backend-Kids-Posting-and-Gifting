<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Media
 *
 * @property integer $id
 * @property string $url
 * @property string $filename
 * @property string $mime_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereFilename($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereMimeType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Media whereIsEnabled($value)
 */
class Media extends Model
{
	/**
     * Hide attributes
     */
    protected $hidden = ['id', 'filename', 'mime_type', 'created_at', 'updated_at'];    
}
