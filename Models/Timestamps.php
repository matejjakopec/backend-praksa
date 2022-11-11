<?php

namespace Models;

use Carbon\Carbon;

trait Timestamps
{
    public function timestampsInsert(): string{
        return 'created_at, updated_at, deleted_at';
    }

    public function timestampsValues(): string{
        $date = Carbon::now("GMT+1");
        return "'{$date}', '{$date}', NULL";
    }

    public function updateTimestamp(): string{
        $date = Carbon::now("GMT+1");
        return "updated_at = '{$date}'";
    }


    public function addDeletedTimestamps($table, $pdo, $id){
        $date = Carbon::now("GMT+1");
        $sql = "UPDATE {$table} 
                SET deleted_at = '{$date}'
                 WHERE id = '{$id}'";
        $pdo->exec($sql);
    }

}