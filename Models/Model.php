<?php



require_once SITE_ROOT . '/Database/Connection.php';
require_once SITE_ROOT . '/Models/Timestamps.php';


class Model
{
    use Timestamps;

    protected $attributes;

    protected $allowed;

    protected $table;

    private $pdo;

    public function __construct(){
        $this->openConnection();
    }

    private function openConnection(){
        $connection = new Connection;
        $this->pdo = $connection->connect();
    }

    private static function getConnection(){
        $connection = new Connection;
        return $connection->connect();
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
        $this->openConnection();
    }

    public function __sleep(){
        $this->pdo = null;
        return ['attributes', 'allowed', 'table'];
    }

    //if it wasn't saved before, creates new empty entry(to get an ID), then it populates it with data

    public function save(){
        if(!array_key_exists('id', $this->attributes)){
            $sql = "INSERT INTO {$this->table} VALUES()";
            $this->pdo->exec($sql);
            $id = $this->pdo->lastInsertId();
            $this->attributes['id'] = $id;
            $this->addCreatedTimestamps($this->table, $this->pdo, $id);
        }
        $sql = "UPDATE {$this->table} SET ";
        foreach ($this->attributes as $key => $attribute){
            if($key != 'id'){
                $sql .= $key . " = '" . $attribute . "', ";
            }
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE id = {$this->attributes['id']}";
        $this->pdo->exec($sql);
        $this->addUpdatedTimestamps($this->table, $this->pdo, $this->attributes['id']);

    }

    public static function all($table){
        $pdo = Model::getConnection();
        $sql = "SELECT * FROM {$table} WHERE deleted_at IS NULL";
        $statement = $pdo->query($sql);
        $rawOutput = $statement->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        foreach ($rawOutput as $item){
            $output[] = Model::convertToObject($item, $table);
        }
        return $output;
    }

    public static function findById($table, $id){
        $pdo = Model::getConnection();
        $sql = "SELECT * FROM {$table}  WHERE id = {$id}";
        $statement = $pdo->query($sql);
        $output = $statement->fetch(PDO::FETCH_ASSOC);
        return Model::convertToObject($output, $table);
    }

    public static function findByProperty($table, $propertyName, $value){
        $pdo = Model::getConnection();
        $sql = "SELECT * FROM {$table}  WHERE {$propertyName} = '{$value}'";
        $statement = $pdo->query($sql);
        $rawOutput = $statement->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        foreach ($rawOutput as $item){
            $output[] = Model::convertToObject($item, $table);
        }
        return $output;
    }

    private static function convertToObject(array $data, $table){
        $model = new Model();
        $model->attributes = $data;
        $model->table = $table;
        $allowed = [];
        foreach ($data as $key => $datum){
            if(!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at'])){
                $allowed[] = $key;
            }
        }
        $model->allowed = $allowed;
        return $model;
    }

    public function delete(){
        $this->addDeletedTimestamps($this->table, $this->pdo, $this->attributes['id']);
    }

    public function forceDelete(){
        $sql = "DELETE FROM {$this->table} WHERE id = {$this->attributes['id']}";
        $this->pdo->exec($sql);
    }

    public function createTable(){
        $sql = "CREATE TABLE {$this->table} ( id bigint NOT NULL AUTO_INCREMENT, ";
        foreach ($this->attributes as $key => $attribute){
            $sql .= $key . " varchar(255), ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ',PRIMARY KEY (id))';
        $this->pdo->exec($sql);
        $this->createTableTimestamps($this->table, $this->pdo);
    }

}