<?php

    use \App\Controllers\Admin\StudentController;
    use \App\Controllers\Admin\StudentBeltController;
    use \App\Http\Response;
    use App\Models\Student;

    $router->get('/admin/aluno', [
        'middlewares' => ['required-login'],
        function($request){
            return new Response(200, StudentController::getIndex($request));
        }
    ]);

    $router->post('/admin/aluno/cadastrar', [
        'middlewares' => ['required-login'],
        function($request){
            return new Response(200, StudentController::postCreate($request));
        }
    ]);

    $router->get('/admin/aluno/cadastrar', [
        function($request){
            return new Response(200, StudentController::getCreate($request));
        }
    ]);

    $router->get('/admin/aluno/editar', [
        function($request){
            return new Response(200, StudentController::getUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/editar', [
        function($request){
            return new Response(200, StudentController::postUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/excluir', [
        function($request){
            return new Response(200, StudentController::postDelete($request));
        }
    ]);


    $router->get('/admin/aluno/graduacao', [
        function ($request){
            return new Response(200, StudentBeltController::getIndex($request));
        }
    ]);

    $router->get('/admin/aluno/graduacao/cadastrar', [
        function($request){
            return new Response(200, StudentBeltController::getCreate($request));
        }
    ]);


    $router->post('/admin/aluno/graduacao/cadastrar', [
        function ($request){
            return new Response(200, StudentBeltController::postCreate($request));
        }
    ]);

    $router->get('/admin/aluno/graduacao/editar', [
        function ($request){
            return new Response(200, StudentBeltController::getUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/graduacao/editar', [
        function ($request){
            return new Response(200, StudentBeltController::postUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/graduacao/excluir', [
        function ($request){
            return new Response(200, StudentBeltController::postDelete($request));
        }
    ]);