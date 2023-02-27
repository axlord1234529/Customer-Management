<?php

namespace App\database;

use App\util\Log;

class Db
{
    private static $_instance = null;
    private $_pdo;

    private function __construct()
    {
        try
        {
            $this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (\PDOException $e)
        {
            Log::write('error',$e->getMessage());
        }
    }

    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new DB();
        }
        return self::$_instance;
    }
    public function query($sql,$params = array()){
        try {
            if($_query = $this->_pdo->prepare($sql)){
                if(count($params)){
                    $position = 1;
                    foreach($params as $param)
                    {
                        $_query->bindValue($position,$param);
                        $position++;
                    }
                }
                if($_query->execute()){
                    $tmp = explode(" ",$sql);
                    if($tmp[0]==='SELECT')
                    {
                        return $_query->fetchAll(PDO::FETCH_OBJ);
                    }
                    return $_query->rowCount();

                }
            }
        }
        catch (\PDOException $e)
        {
            Log::write('error', $e->getMessage());
        }
        return false;
    }

    private function action($action,$table,$where = array()) {
        if(count($where)===3){
            $operators = array('=','>','<','>=','<=');
            $field    = $where[0];
            $operator = $where[1];
            $value    = $where[2];

            if(in_array($operator,$operators)){
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if($this->query($sql,array($value))){
                    return $this;
                }
            }
        }
        else
        {
            $sql = "{$action} FROM {$table}";
            if($this->query($sql)){
                return $this;
            }
        }
    }
    public function get($table,$where = array()) {
        return $this->action('SELECT *',$table,$where);
    }

    public function delete($table,$where = array()) {
        return $this->action('DELETE',$table,$where);
    }

    public function insert($table, $fields = array()){
        $keys = array_keys($fields);
        $values = '';
        $x = 1;
        foreach($fields as $field)
        {
            $values .= '?';
            if($x < count($fields))
            {
                $values .= ' ,';
            }
            $x++;
        }

        $sql ="INSERT INTO {$table}("."`".implode('`,`',$keys)."`) VALUES({$values})";

        if($this->query($sql,$fields))
        {
            return true;
        }
        return false;
    }

    public function update($table_name,$fields = array(),$where = array()){
        $set = '';
        $x = 1;
        foreach($fields as $name => $value)
        {
            $set .= "{$name} = ?";
            if($x < count($fields))
            {
                $set .= ",";
            }
            $x++;
        }
        if(isset($where)&&count($where)===3)
        {
            $operators = array('=','>','<','>=','<=');
            $field    = $where[0];
            $operator = $where[1];
            $value    = $where[2];
            if(in_array($operator,$operators))
            {
                $sql = "UPDATE {$table_name} SET {$set} WHERE {$field} {$operator} ?";
                $fields['where_value'] = $value;
            }
        }else
        {
            $sql = "UPDATE {$table_name} SET {$set}";
        }
        if($this->query($sql,$fields))
        {
            return true;
        }
        return false;
    }




}