<?php 

/*
 * 数据库模型基类
 * */

namespace fool;

abstract class Model
{
    protected $table;

    protected $query;

    protected $data;

    /*
     * 查询到数据，赋值给data
     * */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function newInstance($data)
    {
        return new static($data);
    }

    /*
     * 数据集 转化为数组
     * */
    public function toArray()
    {
    
    }

    /*
     * 通过该方法实现：直接通过model读取查询到的数据的具体字段
     * */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : '';
    }

    /*
     * 通过该方法实现，直接通过model调用query对象的方法
     * */
    public function __call($method, $args)
    {
        $query = $this->getQuery();
        return call_user_func_array([$query, $method], $args);
    }

    /*
     * 通过该方法实现：静态调用query对象的方法
     * */
    public static function __callStatic($method, $args) 
    {
        $model = new static();
        $query = $model->getQuery();
        return call_user_func_array([$query, $method], $args);
    }

    protected function buildQuery()
    {
        $query = new Query($this);
        
        // 设置表名
        $query->setTable($this->table);

        return $query;
    }

    protected function getQuery()
    {
        if (!$this->query) {
            $this->query = $this->buildQuery();
        }
        return $this->query;
    }

    public function save($data)
    {
    
    }

    /*
     * 返回新增的对象
     * */
    public static function create($data)
    {
        $model = new static();

        $model->save($data);
    }

    // 查询一条，多条的方法用query的
}
