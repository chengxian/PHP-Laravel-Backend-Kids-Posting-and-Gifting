<?php
/**
 * @author: chengxian
 * Date: 5/7/16
 * @copyright Cheng Xian Lim
 */


namespace App\Exceptions;


use Exception;

class NotEnabledException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        if ($message == "") {
            $message = "Not Enabled";
        }
        return parent::__construct($message, $code, $previous);
    }
}