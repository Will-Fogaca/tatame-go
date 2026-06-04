<?php

use \App\Controllers\Admin\ClassController;
use \App\Http\Response;

$router->get('/admin/aula', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassController::getIndex($request));
    }
]);

$router->get('/admin/aula/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassController::getCreate($request));
    }
]);

$router->post('/admin/aula/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassController::postCreate($request));
    }
]);

$router->get('/admin/aula/editar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassController::getUpdate($request));
    }
]);

$router->post('/admin/aula/editar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassController::postUpdate($request));
    }
]);

$router->post('/admin/aula/excluir', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassController::postDelete($request));
    }
]);