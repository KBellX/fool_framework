<?php

namespace fool;

use fool\Config;
use fool\exception\ConfigException;
use fool\exception\FileException;

/*
 * 日志类
 * */

class Logger
{
    /*
     * 日志类型（等级）
     * */
    const INFO = 'INFO';

    const DEBUG = 'DEBUG';

    const WARNING = 'WARNING';

    const ERROR = 'ERROR';

    const SQL = 'SQL';
    /*
     * 是否init了
     * */
    private static $isInit = false;

    /*
     * @ var array 写入驱动类型
     * */
    private static $driverType = ['db', 'file'];

    /*
     * @ var string 写入驱动
     * */
    private static $driver = 'file';

    /*
     * @ array 配置信息
     * */
    private static $config = [];

    /*
     * @ array 写入（类型）等级
     * */
    private static $allowSaveType = ['INFO', 'DEBUG', 'WARNING', 'ERROR', 'SQL'];

    /*
     * 初始化
     * 何时调用？public?
     * */
    public static function init()
    {
        $config = Config::get('log');       

        if (isset($config['driver'])  ) {
            static::$driver = $config['driver'];
        } 

        switch (static::$driver) {
        case 'file':
            static::$config['path'] = isset($config['path']) ? $config['path'] : RUNTIME_PATH . 'log' . DS;
            break;
        case 'db':
            break;
        default:
            throw new ConfigException($config['driver'] . ' log driver is not allowed');
        }

        if (isset($config['allow_save_type'])) {
            static::$allowSaveType = $config['allow_save_type'];
        } 

        static::$isInit = true;

    }

    /*
     * 提供对外记录日志方法
     * */
    public static function log($type, $msg, $code = null)
    {
        if (!static::$isInit) {
            static::init();
        }

        // 判断等级
        if (!in_array($type, static::$allowSaveType)) {
            return;
        }

        switch (static::$driver) {
        case 'file':
            static::saveToFile($type, $msg, $code);
            break;
        }
    }

    /*
     *  记录到文件
     * */
    protected static function saveToFile($type, $msg, $code) 
    {
        // 文件名: 指定(默认)目录/(类型)/日期
        $dir = static::$config['path'];

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                // 会造成死循环, 不成功也不提醒了。
                // throw new FileException($dir . 'create dir fail');
            }
        }

        $file = $dir . date('Ymd') . '.log';

        // 写入文件内容
        $content = date('Y-m-d H:i:s') . " [{$type}] ";

        if ($code) {
            $content .= "[{$code}] ";
        }

        $content .= $msg . "\n";

        file_put_contents($file, $content, FILE_APPEND);

    }
}
