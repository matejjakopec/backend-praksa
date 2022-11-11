<?php

namespace Models;

class Animal extends Model
{
    protected $allowed = ['name', 'type'];
    protected static $table = 'animal';

    public function setAttributes($attributeName, $value){
        $this->attributes[$attributeName] = $value;
    }


}