<?php

namespace fool;
// Db单例对象

use fool\exception\DbException;

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

    /*********常用query方法start************/
    public function getPdo()
    {
        return $this->pdo;
    }

    // 直接执行sql方法
    public function execSql($sql)
    {
        return $this->pdo->query($sql);
    }

    // 封装的exec方法
    public function exec($sql)
    {
        $affected = $this->pdo->exec($sql);

        if ($affected === false) {
            // 网上说这种情况仍可能是success的
            $err = $this->pdo->errorInfo();
            // '00000' (success) and '01000' (success with warning).
            if ($err[0] === '00000' || $err[0] === '01000') {
                return true;
            } else {
                throw new DbException('sql errorinfo:' . $err[2] . '; errorcode:'. $err[0], 1002);
            }
        }

        return $affected;
    }
    
    // insert
    public function insert($sql) 
    {
        $this->exec($sql);

        // 两次http请求，会用到同一个pdo对象吗？
        // 一次http请求过程中，有可能出现污染pdo对象情况吗？
        return $this->pdo->lastInsertId();
    }

    // 封装的query方法
    public function query($sql) {
        $stm = $this->pdo->query($sql);

        if ($stm === false) {
            $err = $this->pdo->errorInfo();
            throw new DbException('sql errorinfo:' . $err[2] . '; errorcode:'. $err[0], 1003);
        }

        return $stm;
    }

    public function findAll($sql)
    {
        $stm = $this->query($sql);
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findOne($sql)
    {
        $stm = $this->query($sql);
        $row = $stm->fetch(\PDO::FETCH_ASSOC);
        /*
        if ($row === false) {
            return [];
        }
         */

        return $row;
    }

    public function pQuery($sql) 
    {
        $stm = $this->pdo->prepare($sql);

        $stm->bindParam();

        $stm->execute();
    }

    /****************常见query方法end***********/

    // 生成指定配置的pdo实例
    private function newPdo($dbType) {
        $dbCfg = Config::get('database');

        if (!isset($dbCfg[$dbType])){
            throw new DbException('Database config has error!', 1001);
        }

        $cfg = $this->getCfgStr($dbCfg[$dbType]);

        try{
            $dbh = new \PDO($cfg, $dbCfg[$dbType]['username'], $dbCfg[$dbType]['password'], [
                \PDO::ATTR_PERSISTENT => true    // 持久化连接
            ]); 
        }catch (\PDOException $e){
            throw new DbException($e->getMessage(), 1000);
        }

        return $dbh;
    }

    // 处理配置
    private function getCfgStr ($config) {
        $format = '%s:host=%s;dbname=%s;charset=%s';
        return sprintf($format, 'mysql', $config['host'], $config['dbname'], $config['charset']);
    }

}

