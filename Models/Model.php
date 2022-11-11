<?php

namespace Models;

use Database\Connection;
use PDO;


class Model
{
    use Timestamps;

    protected $attributes;

    protected $allowed;

    protected static $table;

    private $pdo;

    private static $forbiddenKeys = ['id', 'created_at', 'updated_at', 'deleted_at'];


    public static function getTable()
    {
        return static::$table;
    }

    public function __construct(){
        $this->pdo = Connection::getInstance();
    }

    private static function getConnection(){
        return Connection::getInstance();
    }

    public function toArray(){
        return $this->attributes;
    }

    public function __get($name){
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
    }

    public function __set($name, $value){
        if (in_array($name, $this->allowed)) {
            $this->attributes[$name] = $value;
        }
    }

    public function __toString(){
        $output = '';
        foreach ($this->attributes as $key => $attribute){
            $output .= "{$key} : {$attribute} ,";
        }
        return $output;
    }

    public function __call($method, $args)
    {
        return "Method {$method} is not defined";
    }

    public function __isset($name){
        return isset($this->attributes[$name]);
    }

    public function __unset($name){
        unset($this->attributes[$name]);
    }

    public function __wakeup(){
        $this->pdo = Connection::getInstance();
    }

    public function __sleep(){
        $this->pdo = null;
        return ['attributes', 'allowed', 'table'];
    }

    public function save(){
        $table = static::$table;
        if(!array_key_exists('id', $this->attributes)){
            $sql = "INSERT INTO {$table} (";
            foreach ($this->attributes as $key => $attribute){
                if($key != 'id'){
                    $sql .= "{$key}, ";
                }
            }
            $sql .= $this->timestampsInsert();
            $sql .= ') VALUES(';
            foreach ($this->attributes as $key => $attribute){
                if($key != 'id'){
                    $sql .= "'{$attribute}', ";
                }
            }
            $sql .= $this->timestampsValues();
            $sql .= ')';
            $this->pdo->exec($sql);
            $id = $this->pdo->lastInsertId();
            $this->attributes['id'] = $id;
        }else{
            $sql = "UPDATE {$table} SET ";
            foreach ($this->attributes as $key => $attribute){
                if($key != 'id'){
                    $sql .= $key . " = '" . $attribute . "', ";
                }
            }
            $sql .= $this->updateTimestamp();
            $sql .= " WHERE id = {$this->attributes['id']}";
            $this->pdo->exec($sql);
        }
    }

    public static function all(){
        $pdo = Model::getConnection();
        $table = static::getTable();
        $sql = "SELECT * FROM {$table} WHERE deleted_at IS NULL";
        $statement = $pdo->query($sql);
        $rawOutput = $statement->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        foreach ($rawOutput as $item){
            $output[] = Model::convertToObject($item, static::getTable());
        }
        return $output;
    }

    public static function findById($id){
        $pdo = Model::getConnection();
        $table = static::getTable();
        $sql = "SELECT * FROM {$table}  WHERE id = {$id}";
        $statement = $pdo->query($sql);
        $output = $statement->fetch(PDO::FETCH_ASSOC);
        return Model::convertToObject($output, $table);
    }

    public static function findByProperty($propertyName, $value){
        $pdo = Model::getConnection();
        $table = static::getTable();
        $sql = "SELECT * FROM {$table}  WHERE {$propertyName} = '{$value}'";
        $statement = $pdo->query($sql);
        $rawOutput = $statement->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        foreach ($rawOutput as $item){
            $output[] = Model::convertToObject($item);
        }
        return $output;
    }

    private static function convertToObject(array $data){
        $model = new Model();
        $model->attributes = $data;
        $allowed = [];
        foreach ($data as $key => $datum){
            if(!in_array($key, self::$forbiddenKeys)){
                $allowed[] = $key;
            }
        }
        $model->allowed = $allowed;
        return $model;
    }

    public function delete(){
        $this->addDeletedTimestamps(static::$table, $this->pdo, $this->attributes['id']);
    }

    public function forceDelete(){
        $table = static::$table;
        $sql = "DELETE FROM {$table} WHERE id = {$this->attributes['id']}";
        $this->pdo->exec($sql);
    }

    public function createTable(){
        $table = static::$table;
        $sql = "CREATE TABLE {$table} ( id bigint NOT NULL AUTO_INCREMENT, ";
        foreach ($this->attributes as $key => $attribute){
            $sql .= $key . " varchar(255), ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ',PRIMARY KEY (id))';
        $this->pdo->exec($sql);
        $this->createTableTimestamps(static::$table, $this->pdo);
    }

}