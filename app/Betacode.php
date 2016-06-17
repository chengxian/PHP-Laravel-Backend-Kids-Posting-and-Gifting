<?php

namespace App;

use App\Traits\UniqueCode;
use Illuminate\Database\Eloquent\Model;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */

/**
 * App\Betacode
 *
 * @property integer $id
 * @property string $email
 * @property string $betacode
 * @property boolean $used
 * @property string $created_at
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereBetacode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereUsed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereCreatedAt($value)
 * @mixin \Eloquent
 * @property boolean $used
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereUsed($value)
 * @property string $deleted_at
 * @property boolean $is_enabled
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Betacode whereIsEnabled($value)
 */
class Betacode extends Model
{
    use UniqueCode;

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    public function generateCode()
    {
        return $this->generateCodeForCol('betacode');
    }
}
