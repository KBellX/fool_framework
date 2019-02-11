<?php

namespace fool;

// 构建sql

class Builder
{
    protected $exp = [
        'like' => 'LIKE',
        'in' => 'IN',
        'not in' => 'NOT IN',
        '!=' => '!=',
        '>' => '>',
        '>=' => '>=',
        '<' => '<',
        '<=' => '<=',
    ];
    protected $insertSql = 'INSERT INTO %TABLE% (%FIELD%) VALUES (%DATA%)';
    protected $selectSql = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%UNION%%ORDER%%LIMIT%%LOCK%%COMMENT%';
    protected $updateSql    = 'UPDATE %TABLE% SET %SET% %JOIN% %WHERE% %ORDER%%LIMIT% %LOCK%%COMMENT%';

    public function insert($data, $options)
    {
        $fields = array_keys($data);
        $values = array_values($data);

        $sql = str_replace(
            ['%TABLE%', '%FIELD%', '%DATA%'],
            [
                $this->parseTable($options['table']),
                    implode(',', $fields),
                    implode(',', $values),
                ],
                $this->insertSql
            );

        return $sql;
    }

    public function update($data, $options)
    {
        foreach ($data as $field => $value) {
            $set[] = $field . '=' . $value;
        }

        $sql = str_replace(
            ['%TABLE%', '%SET%', '%JOIN%', '%WHERE%', '%ORDER%', '%LIMIT%', '%LOCK%', '%COMMENT%'],
            [
                $this->parseTable($options['table'], $options),
                    implode(',', $set),
                    $this->parseJoin($options['join'], $options),
                    $this->parseWhere($options['where'], $options),
                    $this->parseOrder($options['order'], $options),
                    $this->parseLimit($options['limit']),
                    $this->parseLock($options['lock']),
                    $this->parseComment($options['comment']),
                ], $this->updateSql);

        // echo $sql;die;

        return $sql;    
    }

    public function select($options = [])
    {
        $sql = str_replace(
            ['%TABLE%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'],
            [
                $this->parseTable($options['table']),
                    $this->parseDistinct($options['distinct']),
                    $this->parseField($options['field'], $options),
                    $this->parseJoin($options['join'], $options),
                    $this->parseWhere($options['where'], $options),
                    $this->parseGroup($options['group']),
                    $this->parseHaving($options['having']),
                    $this->parseOrder($options['order'], $options),
                    $this->parseLimit($options['limit']),
                    $this->parseUnion($options['union']),
                    $this->parseLock($options['lock']),
                    $this->parseComment($options['comment']),
                    $this->parseForce($options['force']),
                ],
                $this->selectSql
            );

        // echo $sql;die;

        return $sql;
    }

    protected function parseTable($table) 
    {
        return $table;
    }

    protected function parseDistinct($distinct) 
    {
        return !empty($distinct) ? 'DISTINCT' : '';
    }

    protected function parseField($fields, $options) 
    {
        if ($fields == '*' || empty($fields)) {
            $fieldsStr = '*';
        } elseif (is_array($fields)) {
            $fieldsStr = implode(',', $fields);
        }
        return $fieldsStr;
    }

    protected function parseJoin($join, $options) 
    {
        return '';
    }

    protected function parseWhere($where, $options) 
    {
        $whereStr = '';
        foreach ($where as $field => $val) {
            // 一个字段两个条件如何描述
            if (is_array($val)) {
                $whereStr .= $this->buildWhere($field, $val);
            } else {
                $whereStr .= $field . '=' . "'$val'";
            }

            $whereStr .= ' AND ';
        }

        $whereStr = rtrim($whereStr, ' AND ');

        // var_dump($whereStr);die;

        return !empty($where) ? ' WHERE ' . $whereStr : '';
    }

    // 给where和whereOr用
    protected function buildWhere($field, $val)
    {
        $str = '';

        if (in_array($val[0], ['in', 'not in'])) {
            if (is_array($val[1])) {
                foreach($val[1] as &$v) {
                    $v = "'$v'";
                }
                $val[1] = implode(',', $val[1]);
            }
            $val[1] = '(' . $val[1] . ')';
            $str =  $field . ' ' . $this->exp[$val[0]] . ' ' . "$val[1]";
        }

        if (in_array($val[0], ['like', '>', '>=', '<', '<=', '!='])) {
            $str =  $field . ' ' . $this->exp[$val[0]] . ' ' . "'$val[1]'";
        }     

        // var_dump($str);die;

        return $str;
    }

    protected function parseGroup($group) 
    {
        return '';
    }

    protected function parseHaving($join) 
    {
        return '';
    }

    protected function parseLimit($limit) 
    {
        $limitStr = '';
        if (isset($limit[0])) {
            if (!isset($limit[1])) {
                $limitStr = $limit[0];
            } else {
                $limitStr = "$limit[0],$limit[1]";
            }
        }
        return !empty($limit) ? ' LIMIT ' . $limitStr : '';
    }

    protected function parseUnion($union) 
    {
        return '';
    }

    protected function parseLock($lock) 
    {
        return '';
    }

    protected function parseComment($comment) 
    {
        return '';
    }

    protected function parseForce($force) 
    {
        return '';
    }

    protected function parseOrder($order, $options) 
    {
        $sort = [
            'asc' => 'ASC',
            'desc' => 'DESC'
        ];
        if (empty($order)) {
            return '';
        }

        $orderStr = '';
        foreach ($order as $field => $o) {
            if (is_numeric($field)) {
                $orderStr .= $o . ',';
            } else {
                $orderStr .= $field . " {$sort[$o]}";
            }
        }
        $orderStr = rtrim($orderStr, ',');

        return !empty($order) ? ' ORDER BY ' . $orderStr : '';
    }
}
