<?php

namespace fool\exception;

use fool\Exception;

class FileException extends Exception
{
    public function __construct($msg, $code = 1200)
    {
        parent::__construct($msg, $code);
    }
}

