<?php


/**
 * @author: chengxian
 * Date: 4/11/16
 * @copyright Cheng Xian Lim
 */
class DummyModelEncrypting extends DummyModel
{
    protected $encrypts = [
        'phone',
        'cell'
    ];
}