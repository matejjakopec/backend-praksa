<?php

namespace Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TestController
{
    public function fun1(){
        $loader = new FilesystemLoader('Twig/Templates');
        $twig = new Environment($loader);
        $function = new TwigFunction('add', function ($a, $b) {
            return $a + $b;
        });
        $twig->addFunction($function);
        echo $twig->render('index.html', ['name' => 'Matej', 'high' => '100']);
    }

    public function fun2(){
        echo 'TestController fun22';
    }
}