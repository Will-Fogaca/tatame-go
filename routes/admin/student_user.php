<?php

use \App\Controllers\Admin\StudentUserController;
use \App\Http\Response;

$router->get('/admin/vinculos', [
    function($request){
        return new Response(200, StudentUserController::getIndex($request));
    }
]);

$router->get('/admin/vinculos/cadastrar', [
    function($request){
        return new Response(200, StudentUserController::getCreate($request));
    }
]);

$router->post('/admin/vinculos/buscar', [
    function($request){
        return new Response(200, StudentUserController::postSearch($request));
    }
]);

$router->post('/admin/vinculos/cadastrar', [
    function($request){
        return new Response(200, StudentUserController::postCreate($request));
    }
]);

$router->post('/admin/vinculos/excluir', [
    function($request){
        return new Response(200, StudentUserController::postDelete($request));
    }
]);