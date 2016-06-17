<?php

namespace App;

use App\Enums\NotificationFrequencyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Setting
 *
 * @property integer $id
 * @property integer $user_id
 * @property boolean $allow_notification
 * @property string $notification_frequency
 * @property float $donation_percent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereAllowNotification($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereNotificationFrequency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereDonationPercent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereIsEnabled($value)
 */
class Setting extends Model
{
    protected $attributes = [
        'allow_notification' => true,
        'notification_frequency' => NotificationFrequencyType::REALTIME,
        'donation_percent' => 0.05
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
