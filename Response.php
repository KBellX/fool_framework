<?php

namespace fool;

class Response
{
    private static $data = [
        'err_code' => 0,
        'msg' => '',
        'data' => [],
    ];

    public static function setData($data, $msg = null, $code = null)
    {
        static::$data['data'] = $data;

        if ($msg) {
            static::$data['msg'] = $msg;
        }

        if ($code) {
            static::$data['err_code'] = $code;
        }
    }

    public static function getData()
    {
        return static::$data;
    }

    public static function send()
    {
        echo json_encode(static::$data);
    }
}
