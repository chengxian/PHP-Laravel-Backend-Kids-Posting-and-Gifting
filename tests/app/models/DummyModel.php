<?php


/**
 * @author: chengxian
 * Date: 4/11/16
 * @copyright Cheng Xian Lim
 */
class DummyModel extends \Illuminate\Database\Eloquent\Model
{
    use \Kidgifting\LaraVault\LaraVault;

    protected $table = 'dummy';

    public function getDummyAttributes()
    {
        return $this->attributes;
    }

    public function setDummyAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function save(array $options = [])
    {
        $this->fireModelEvent('saving');
        return true;
    }

}

