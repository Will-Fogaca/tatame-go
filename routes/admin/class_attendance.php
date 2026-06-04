<?php

use \App\Controllers\Admin\ClassAttendanceController;
use \App\Http\Response;

$router->get('/admin/aula/presenca', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassAttendanceController::getIndex($request));
    }
]);

$router->post('/admin/aula/presenca/salvar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassAttendanceController::postSave($request));
    }
]);