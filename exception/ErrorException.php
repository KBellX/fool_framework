<?php

namespace fool\exception;

use fool\Exception;

class ErrorException extends Exception
{
    public function __construct($errno, $errstr, $errfile, $errline = 0)
    {
        $msg = "$errfile 第 $errline 行 : [{$errno}] $errstr ";

        $code = 1300;

        parent::__construct($msg, $code);
    }
}

