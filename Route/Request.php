<?php

namespace Route;

class Request
{

    public function get($name){
        if(isset($_GET[$name])){
            return $_GET[$name];
        }
        return null;
    }
}