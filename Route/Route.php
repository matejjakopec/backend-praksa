<?php

namespace Route;

class Route
{

    public static $routes = [];

    public static function match(){
        $endpoints = array_keys(self::$routes);
        foreach ($endpoints as $endpoint){
            $exploded = explode("_", $endpoint);
            $method = end($exploded);
            array_pop($exploded);
            $url = implode($exploded);
            if(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == $url && $method == $_SERVER['REQUEST_METHOD']){
                $info = self::$routes[$endpoint];
                $class = new $info[0]();
                $method = $info[1];
                $class->$method(new Request());
            }
        }

    }

    public static function get($route, $controllerClass, $methodName){
        self::$routes[$route . '_GET'] = [$controllerClass, $methodName];
    }

    public static function post($route, $controllerClass, $methodName){
        self::$routes[$route . '_POST'] = [$controllerClass, $methodName];
    }

}