<?php

namespace fool\exception;

use fool\Exception;

class ConfigException extends Exception
{
    public function __construct($msg = 'config exception', $code = 1100)
    {
        parent::__construct($msg, $code);
    }
}

