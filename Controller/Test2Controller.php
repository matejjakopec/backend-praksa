<?php

namespace Controller;

use Route\Request;

class Test2Controller
{
    public function fun1(Request $request){
        echo 'Test2Controller fun11'  . $request->get('name');
    }

    public function fun2(){
        echo 'Test2Controller fun22';
    }

}