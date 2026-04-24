<?php

    use \App\Controllers\Admin\StudentController;
    use \App\Http\Response;

    $router->get('/admin/aluno', [
        'middlewares' => ['required-login'],
        function($request){
        return new Response(200, StudentController::getIndex($request));
        }
    ]);

    $router->post('/admin/aluno/cadastro', [
        'middlewares' => ['required-login'],
        function($request){
        return new Response(200, StudentController::postStore($request));
        }
    ]);

    $router->get('/admin/aluno/cadastro', [
        
        function($request){
        return new Response(200, StudentController::getStore($request));
        }
    ]);
