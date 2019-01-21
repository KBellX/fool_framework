<?php

namespace fool;

use fool\exception\Handle;
use fool\exception\ErrorException;

class Error
{
    /*
     * 注册异常处理
     * @return void
     * */
    public static function register()
    {
        error_reporting(E_ALL);
        set_exception_handler([__CLASS__, 'appException']);
        // 生成环境，自定义处理所有错误。
        if (ENV == 'prod') {
            set_error_handler([__CLASS__, 'appError']);
            // fatal错误能捕获但仍会抛出，且无法捕获后面的异常
            // register_shutdown_function([__CLASS__, 'appShutdown']);
        }
    }

    /*
     * 异常处理函数
     * @param \Exception
     * @return void
     * */
    public static function appException($e)
    {
        $handler = self::getExceptionHandler();
        $handler->render($e);
    }

    /*
     * Error处理函数
     * 生成环境：将错误信息抛异常，作异常处理
     * */
    public static function appError($errno, $errstr, $errfile, $errline = 0)
    {
        if (error_reporting() & $errno) {
            throw new ErrorException($errno, $errstr, $errfile, $errline);
        } 
    }

    /*
     * Shutdown Handler
     * */
    public static function appShutdown()
    {
        if (!is_null($error = error_get_last()) && static::isFatal($error['type'])) {
            throw new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /*
     * 判断错误是否致命
     * */
    protected static function isFatal($type) 
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /*
     * 获取异常处理实例
     * @return Handle
     * */
    private static function getExceptionHandler()
    {
        // 如果没定义，就用默认的
        // 有定义，要判断是否继承了 异常处理基类
        $handler = new Handle();
        return $handler;
    }
}
