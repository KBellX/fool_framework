<?php

namespace fool;

/*
 * 调用builder 和 db(connection)
 * */

class Query
{
    protected $db;

    protected $builder;

    protected $options;

    public function __construct()
    {
        $this->db = Db::instance();

        $this->db->useDb('read');

        $this->builder = new Builder();
    }

    protected function getOptions()
    {
        return $this->options;
    }

    /********最终操作begin*************/
    /*
     * 新增
     * */
    public function insert(array $data = [])
    {
        $sql = $this->builder->insert($this->parseData($data), $this->getOptions());

        return $this->db->insert($sql);
    }

    /*
     * 查找单挑记录
     * */
    public function find()
    {

    }

    /*
     * 查找记录
     * */
    public function select()
    {
        $options = $this->parseExpress();
        $sql = $this->builder->select($options);

        echo $sql;

        return $this->db->findAll($sql);
    }

    /********最终操作end*************/

    /********链式操作begin*************/
    /*
     * @param $distinct bool
     * @return $this
     * */
    public function distinct($distinct) 
    {
        $this->options['distinct'] = $distinct;
        return $this;
    }

    /*
     * @param mixed $table 表名
     * @return $this
     * */
    public function table($table)
    {
        $this->options['table'] = $table;
        return $this;
    }

    public function field($field)
    {
        $this->options['field'] = $field;
        return $this;
    }

    public function where($condition)
    {
        // 处理闭包
        
        // 组装成以OR分隔的数组
        $this->options['where']['']
        $this->options['where'] = $condition;
        return $this;   
    }

    public function whereOr($condition)
    {
       return $this; 
    }

    /********链式操作end*************/

    protected function parseData($data)
    {
        foreach ($data as &$value) {
            $value =  "'$value'";
        }

        return $data;
    }

    protected function parseExpress()
    {
        $options = $this->options;

        if (!isset($options['field'])) {
            $options['field'] = '*';
        }

        foreach(['distinct', 'join', 'where', 'group', 'having', 'order', 'limit', 'union', 'lock', 'comment', 'force'] as $name) {
            if (!isset($options[$name])) {
                $options[$name] = '';
            }
        }

        return $options;
    }


}
