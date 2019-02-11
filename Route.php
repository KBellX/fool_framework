<?php

namespace fool;

use fool\exception\ApiException;
use fool\helper\Format;

class Route
{
    private static $controller;

    private static $action;

    public static function parse()
    {
        $uri = $_SERVER['REQUEST_URI'];

        // 去除/api
        $uri = substr($uri, 4); 

        // 去除queryString
        if ($_SERVER['QUERY_STRING']) {
            $uri = strstr($uri, '?', true);
        }

        // /controller/action
        $route = explode('/', $uri);

        if (!isset($route[1]) || !isset($route[2])) {
            throw new ApiException('The require uri is not valid!');
        }

        // 控制器 下划线 => 首字母大写驼峰
        static::$controller = Format::camelize($route[1]);
        // action 下划线
        static::$action = $route[2];
    }

    public static function getController()
    {
        return static::$controller;
    }

    public static function getAction()
    {
        return static::$action;
    }
}
