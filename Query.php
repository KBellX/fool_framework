<?php

namespace fool;

/*
 * 调用builder 和 db(connection)
 * */

class Query
{
    protected $db;

    protected $builder;

    protected $model;

    protected $options;

    public function __construct($model)
    {
        $this->model = $model;

        $this->db = Db::instance();

        $this->db->useDb('read');

        $this->builder = new Builder();
    }

    protected function getOptions()
    {
        return $this->options;
    }

    // 提供给model设置表名
    public function setTable($table)
    {
        $this->table($table);
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
     * 更新
     * */
    public function update(array $data = [])
    {
        $options = $this->parseExpress();
        $sql = $this->builder->update($this->parseData($data), $options);

        return $this->db->exec($sql);
    }
    /*
     * 查找单挑记录
     * */
    public function find()
    {
        $options = $this->parseExpress();
        $sql = $this->builder->select($options);

        $result = $this->db->findOne($sql);

        // 数据处理：将数组转换成对象
        if ($result) {
            if (!empty($this->model)) {
                $result = $this->model->newInstance($result);   
            }
        }

        return $result;
    }

    /*
     * 查找记录
     * */
    public function select()
    {
        $options = $this->parseExpress();
        $sql = $this->builder->select($options);

        $resultset = $this->db->findAll($sql);

        $resultsetType = Config::get('resultset_type');
        if ($resultsetType == 'collection') {
            if (count($resultset) > 0) {
                foreach ($resultset as $key => $result) {
                    $result = $this->model->newInstance($result);
                    $resultset[$key] = $result;
                }
            }       
        }

        return $resultset;
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

    public function order($order) 
    {
        $this->options['order'] = $order;
        return $this;
    }

    public function limit($limit) 
    {
        $this->options['limit'] = $limit;
        return $this;
    }

    public function page($page) 
    {
        $this->options['limit'] = [($page[0] - 1) * $page[1], $page[1]];
        return $this;
    }

    public function where($condition)
    {
        // 处理闭包

        // 组装成以OR分隔的数组
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
