<?php

namespace fool;
// Db单例对象

class Db {
    private static $db;     // Db类的实例

    private $allPdo = [];    // 所有pdo连接

    private $pdo;   // 当前使用的pdo连接

    private $defaultDbType = 'read';

    // 禁止直接new得到Db实例
    private function __construct () {
    }

    // 提供对外得到Db实例的方法
    public static function instance () {
        // 变量的默认值是?
        if(static::$db == NULL) {
            static::$db = new Db();
        } 

        return static::$db;
    }

    public function useDb($dbType = '') {
        // 函数参数没设默认值又没传会发warning，想动态设置默认值，可参考下面的方式
        if(!$dbType) {
            $dbType = $this->defaultDbType;
        }

        if(!isset($allPdo[$dbType])) {
            $allPdo[$dbType] = $this->newPdo($dbType);
        }

        $this->pdo = $allPdo[$dbType];
    }


    // 对外提供设置默认数据库，一般应该是框架设计者调用的。
    public static function setDefaultDbType($type) {
        static::$defaultDbType = $type;
    }

    /*********常用query方法************/
    public function query($sql) {
        $result = $this->pdo->query($sql);

        $arr = $result->fetchAll();

        return $arr;
    }

    // 生成指定配置的pdo实例
    private function newPdo($dbType) {
        global $CONFIG;   

        $dbCfg = $CONFIG['database'];

        if (!isset($dbCfg[$dbType])){

        }

        $cfg = $this->getCfgStr($dbCfg[$dbType]);

        try{
            $dbh = new PDO($cfg, $dbCfg[$dbType]['username'], $dbCfg[$dbType]['password'], [
                PDO::ATTR_PERSISTENT => true    // 持久化连接
            ]); 
        }catch (PDOException $e){
            var_dump($e->getMessage());
            die;
        }

        return $dbh;
    }

    // 处理配置
    private function getCfgStr ($config) {
        $format = '%s:host=%s;dbname=%s;charset=%s';
        return sprintf($format, 'mysql', $config['host'], $config['dbname'], $config['charset']);
    }

}

