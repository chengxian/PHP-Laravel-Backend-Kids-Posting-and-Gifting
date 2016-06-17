<?php
/**
 * @author: chengxian
 * Date: 4/25/16
 * @copyright Cheng Xian Lim
 */


namespace Kidgifting\LaraVault;


use Eloquent;
use Kidgifting\ThinTransportVaultClient\StringException;
use Kidgifting\ThinTransportVaultClient\TransitClient;

/**
 * Kidgifting\LaraVault\LaraVaultHasher
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\LaraVault\LaraVaultHasher whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\LaraVault\LaraVaultHasher whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\LaraVault\LaraVaultHasher whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\LaraVault\LaraVaultHasher whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Kidgifting\LaraVault\LaraVaultHasher whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LaraVaultHash extends Eloquent
{
    use LaraVault;

    protected $table = 'laravault_hash';

    protected $encrypts = ['value'];

}