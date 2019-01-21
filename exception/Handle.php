<?php

namespace fool\exception;

use Exception;
use fool\Logger;
/*
 * 异常处理类
 *  写成类方便以后扩展
 * */

class Handle
{
    /*
     * @param \Exception $e php根异常
     * @return Response
     * */
    public function render(Exception $e)
    {
        if ($e instanceof ApiException) {
            echo 'this is api exception, render friendly response';
        } else {
            if (ENV == 'dev') {
                // 开发环境, 直接抛出
                throw $e;
            } else {
                // 生成环境, 记录日志
                Logger::log(Logger::ERROR, $e->getMessage(), $e->getCode());
            }
            
        }
    }
}
