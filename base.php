<?php

defined("FOOL_PATH") || define("FOOL_PATH", ROOT_PATH . 'fool' . DS);
defined("APP_PATH") || define("APP_PATH", ROOT_PATH . 'app' . DS);
define('EXT', '.php');
defined("EXTEND_PATH") || define("EXTEND_PATH", ROOT_PATH . 'extend' . DS);
defined("RUNTIME_PATH") || define("RUNTIME_PATH", ROOT_PATH . 'runtime' . DS);

require FOOL_PATH . 'Loader.php';


// 将运行过程可能会用到的工具初始化

// 注册自动加载类
\fool\Loader::register();

// 注册异常处理
\fool\Error::register();

// 加载配置文件
\fool\Config::load();

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 初始化日志类
\fool\Logger::init();
