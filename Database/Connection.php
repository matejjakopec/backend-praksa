<?php

namespace Database;

use PDO;
use PDOException;

require_once 'config.php';

class Connection
{

    private static $instance = null;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $host = $GLOBALS['host'];
            $db = $GLOBALS['db'];
            $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
            try {
                self::$instance = new PDO($dsn, $GLOBALS['user'], $GLOBALS['password']);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        return self::$instance;
    }

}