<?php

require SITE_ROOT . '/vendor/autoload.php';

use Carbon\Carbon;

trait Timestamps
{
    public function createTableTimestamps($table, $pdo){
        $sql = "ALTER TABLE {$table} 
        ADD created_at datetime,
        ADD updated_at datetime,
        ADD deleted_at datetime";
        $this->pdo->exec($sql);
    }

    public function addCreatedTimestamps($table, $pdo, $id){
        $date = Carbon::now("GMT+1");
        $sql = "UPDATE {$table} 
                SET created_at = '{$date}',
                 updated_at = '{$date}'
                 WHERE id = '{$id}'";
        $pdo->exec($sql);
    }

    public function addUpdatedTimestamps($table, $pdo, $id){
        $date = Carbon::now("GMT+1");
        $sql = "UPDATE {$table} 
                SET updated_at = '{$date}'
                 WHERE id = '{$id}'";
        $pdo->exec($sql);
    }

    public function addDeletedTimestamps($table, $pdo, $id){
        $date = Carbon::now("GMT+1");
        $sql = "UPDATE {$table} 
                SET deleted_at = '{$date}'
                 WHERE id = '{$id}'";
        $pdo->exec($sql);
    }

}