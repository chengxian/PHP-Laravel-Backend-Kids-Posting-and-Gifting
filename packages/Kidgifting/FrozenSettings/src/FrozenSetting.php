<?php

namespace Kidgifting\FrozenSettings;

use Cache;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Kidgifting\FrozenSettings\FrozenSetting
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\FrozenSettings\FrozenSetting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\FrozenSettings\FrozenSetting whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\FrozenSettings\FrozenSetting whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\FrozenSettings\FrozenSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\FrozenSettings\FrozenSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 * 
 * To use in settings file:
 * 'before_save' => function (&$data) {
 *     foreach ($data as $key => $value) {
 *         $setting = FrozenSetting::firstOrNew(['key' => $key]);
 *         $setting->value = $value;
 *         $setting->save();
 *     }
 * },
 */
class FrozenSetting extends Model
{
    protected $fillable = ['key'];

    /**
     * @param $key
     * @return Model|static
     * @throws Exception
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function forKey($key)
    {
        if (!is_string($key)) {
            throw new Exception("$key must be a string");
        }

        // TODO configure cache so it can be disabled
//        $value = Cache::remember('settings', 1, function() use ($key) {
//            $setting = FrozenSetting::whereKey($key)->firstOrFail();
//            return $setting->value;
//        });
//
//        return $value;
        return FrozenSetting::whereKey($key)->firstOrFail()->value;
    }
}
