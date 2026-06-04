<?php

use \App\Controllers\Admin\ClassModalityController;
use \App\Http\Response;

$router->get('/admin/aula/modalidade', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassModalityController::getIndex($request));
    }
]);

$router->get('/admin/aula/modalidade/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassModalityController::getCreate($request));
    }
]);

$router->post('/admin/aula/modalidade/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassModalityController::postCreate($request));
    }
]);

$router->get('/admin/aula/modalidade/editar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassModalityController::getUpdate($request));
    }
]);

$router->post('/admin/aula/modalidade/editar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassModalityController::postUpdate($request));
    }
]);

$router->post('/admin/aula/modalidade/excluir', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, ClassModalityController::postDelete($request));
    }
]);