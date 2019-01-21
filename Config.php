<?php

namespace fool;

/*
 * 操作配置文件的类
 * */

use fool\exception\FileException;

class Config
{
    /*
     * @var array 全局配置信息
     * */
    private static $config = [];

    /*
     * 加载配置文件
     * @return void
     * */
    public static function load()
    {
        $env = defined("ENV") ? ENV : 'dev';

        $file = APP_PATH . $env . '_config.php';
        if (!is_file($file)) {
            throw new FileException($file . 'is not exist');
        }

        self::$config = include $file;

        // 本地配置文件
        $localFile = APP_PATH . 'local_config.php';
        if (is_file($localFile)) {
            self::$config = array_merge(self::$config, include $localFile);
        }
    }

    /*
     * 获取指定键值的配置信息(支持三级配置)
     *  需区分大小写
     * @param string $name 逗号分隔的配置键
     * @return mixed
     * */
    public static function get($name = null)
    {
        if (!$name) {
            return self::$config;
        }

        $keys = explode('.', $name, 3);

        if (isset($keys[2]) && isset(self::$config[$keys[0]][$keys[1]][$keys[2]])) {
            return self::$config[$keys[0]][$keys[1]][$keys[2]];
        }

        if (isset($keys[1]) && isset(self::$config[$keys[0]][$keys[1]])) {
            return self::$config[$keys[0]][$keys[1]];
        }

        if (isset($keys[0]) && isset(self::$config[$keys[0]])) {
            return self::$config[$keys[0]];
        }

        return null;


    }

    /*
     * 设置配置数据
     * @param string       $name  逗号分隔的配置键
     * @value string|array $value 值
     * @return mixed
     * */
    public static function set($name, $value)
    {
        $keys = explode('.', $name, 3);   

        if (isset($keys[2])) {
            return self::$config[$keys[0]][$keys[1]][$keys[2]] = $value;
        }

        if (isset($keys[1])) {
            return self::$config[$keys[0]][$keys[1]] = $value;
        }

        if (isset($keys[0])) {
            return self::$config[$keys[0]] = $value;
        }

        return false;
    }
}
