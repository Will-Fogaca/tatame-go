<?php

use \App\Controllers\Admin\StudentUserController;
use \App\Http\Response;

$router->get('/admin/vinculos', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, StudentUserController::getIndex($request));
    }
]);

$router->get('/admin/vinculos/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, StudentUserController::getCreate($request));
    }
]);

$router->post('/admin/vinculos/buscar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, StudentUserController::postSearch($request));
    }
]);

$router->post('/admin/vinculos/cadastrar', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, StudentUserController::postCreate($request));
    }
]);

$router->post('/admin/vinculos/excluir', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, StudentUserController::postDelete($request));
    }
]);