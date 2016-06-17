<?php

/**
 * @author: chengxian
 * Date: 5/1/16
 * @copyright Cheng Xian Lim
 */

namespace App\Interfaces;

interface Allowable
{
    public function isAllowed();
    public function getDisallowedExceptions();
    public function getDisallowedMessages();
    public function getDisallowedMessagesAsString();
}