<?php

namespace fool\exception;

use fool\Exception;

class DbException extends Exception
{
    public function __construct($msg = 'db exception', $code = 1000)
    {
        parent::__construct($msg, $code);
    }

}
