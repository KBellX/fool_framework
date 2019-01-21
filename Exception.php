<?php

namespace fool;

/*
 * 自定义异常基类
 *  所有后续写的异常类都应继承这个，而不是php的根异常类
 * */

class Exception extends \Exception
{
    public function __construct($message = 'general exception', $code = 500)
    {
        parent::__construct($message, $code);
    }
}
