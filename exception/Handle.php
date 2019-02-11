<?php

namespace fool\exception;

// use \Exception;
use fool\Logger;
use fool\Response;
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
    public function render(\Exception $e)
    {
        if ($e instanceof ApiException) {
            Response::setData([], $e->getMessage(), $e->getCode());
        } else {
            if (ENV == 'dev') {
                // 开发环境, 直接抛出
                throw $e;
            } else {

                // 生成环境, 记录具体错误信息的日志
                Logger::log(Logger::ERROR, $e->getMessage(), $e->getCode());
                
                // 统一报系统错误,500
                Response::setData([], 'server error', 500);
            }
            
        }

        Response::send();
    }
}
