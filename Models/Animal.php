<?php

class Animal extends Model
{
    public $allowed = ['name', 'type'];
    public $table = 'animal';

    public function setAttributes($attributeName, $value){
        $this->attributes[$attributeName] = $value;
    }


}