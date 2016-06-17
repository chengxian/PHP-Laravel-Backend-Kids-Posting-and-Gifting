<?php
/**
 * @author: chengxian
 * Date: 5/7/16
 * @copyright Cheng Xian Lim
 */


namespace App\Exceptions;


use Exception;

class UserNotAllowedException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        if ($message == "") {
            $message = "User Not Allowed";
        }
        return parent::__construct($message, $code, $previous);
    }
}