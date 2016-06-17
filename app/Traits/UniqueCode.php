<?php
/**
 * @author: chengxian
 * Date: 3/26/16
 * @copyright Cheng Xian Lim
 */


namespace App\Traits;


use Webpatser\Uuid\Uuid;

trait UniqueCode
{
    protected function generateCodeForCol($col, $level=0)
    {
        if ($level == 5) {
            throw new Exception("Could not generate unique code");
        }
        $code = Uuid::generate(4);
        $testInstance = $this->where($col, '=', $code);

        if ($testInstance->count() == 0) {
            return $code;
        }
        else {
            return $this->generateCodeForCol($col, $level+1);
        }

    }
}