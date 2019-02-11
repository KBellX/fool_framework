<?php

namespace fool;

use fool\exception\ApiException;

class App
{
    public static function run()
    {
        Route::parse();

        $controller = 'app\controller\\' . Route::getController();
        $action = Route::getAction();

        // 如果能捕获所有错误,异常，就能用try,catch，写起来更方便了
        
        if (!class_exists($controller)) {
            throw new ApiException(Route::getController() . ' controller is not exist!');
        }

        if (!in_array($action, get_class_methods($controller))) {
            throw new ApiException($action . ' action is not exist!');
        }

        $c = new $controller();

        $data = $c->$action();

        Response::setData($data);

        Response::send();

    }
}

