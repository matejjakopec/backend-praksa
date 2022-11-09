<?php

require_once 'config.php';

class Connection
{

    public function connect(){
        $host = $GLOBALS['host'];
        $db = $GLOBALS['db'];
        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
        try {
            return new PDO($dsn, $GLOBALS['user'], $GLOBALS['password']);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

}