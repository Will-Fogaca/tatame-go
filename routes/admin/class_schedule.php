<?php

use \App\Controllers\Admin\ClassScheduleController;
use \App\Http\Response;

$router->get('/admin/aula/horario', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassScheduleController::getIndex($request));
    }
]);

$router->get('/admin/aula/horario/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassScheduleController::getCreate($request));
    }
]);

$router->post('/admin/aula/horario/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassScheduleController::postCreate($request));
    }
]);

$router->get('/admin/aula/horario/editar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassScheduleController::getUpdate($request));
    }
]);

$router->post('/admin/aula/horario/editar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassScheduleController::postUpdate($request));
    }
]);

$router->post('/admin/aula/horario/excluir', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassScheduleController::postDelete($request));
    }
]);