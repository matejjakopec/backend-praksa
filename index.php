<?php

require 'vendor/autoload.php';

use Controller\Test2Controller;
use Route\Route;
use Controller\TestController;
use Models\Animal;

Route::get('/asd', TestController::class, 'fun1');
Route::post('/asd', TestController::class, 'fun2');
Route::get('/qwe', Test2Controller::class, 'fun1');
Route::post('/qwe', Test2Controller::class, 'fun2');

Route::match();







