<?php

namespace fool\exception;

use fool\Exception;

/*
 * Api异常基类
 *  用户行为导致异常
 * */

class ApiException extends Exception
{
    public $code = 500;

    public $message = 'api exception';

    public function __construct($message = null, $code = 0)
    {
        if (!$message) {
            $message = $this->message;
        }

        if (!$code) {
            $code = $this->code;
        }

        parent::__construct($message, $code);
    }
}
