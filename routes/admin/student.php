<?php

    use \App\Controllers\Admin\StudentController;
    use \App\Controllers\Admin\StudentBeltController;
    use \App\Http\Response;
    use App\Models\Student;

    $router->get('/admin/aluno', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentController::getIndex($request));
        }
    ]);

    $router->post('/admin/aluno/cadastrar', [
       'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentController::postCreate($request));
        }
    ]);

    $router->get('/admin/aluno/cadastrar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentController::getCreate($request));
        }
    ]);

    $router->get('/admin/aluno/editar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentController::getUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/editar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentController::postUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/excluir', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentController::postDelete($request));
        }
    ]);


    $router->get('/admin/aluno/graduacao', [
        'middlewares'=> ['isAdmin'],
        function ($request){
            return new Response(200, StudentBeltController::getIndex($request));
        }
    ]);

    $router->get('/admin/aluno/graduacao/cadastrar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, StudentBeltController::getCreate($request));
        }
    ]);


    $router->post('/admin/aluno/graduacao/cadastrar', [
        'middlewares'=> ['isAdmin'],
        function ($request){
            return new Response(200, StudentBeltController::postCreate($request));
        }
    ]);

    $router->get('/admin/aluno/graduacao/editar', [
        'middlewares'=> ['isAdmin'],
        function ($request){
            return new Response(200, StudentBeltController::getUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/graduacao/editar', [
        'middlewares'=> ['isAdmin'],
        function ($request){
            return new Response(200, StudentBeltController::postUpdate($request));
        }
    ]);

    $router->post('/admin/aluno/graduacao/excluir', [
        'middlewares'=> ['isAdmin'],
        function ($request){
            return new Response(200, StudentBeltController::postDelete($request));
        }
    ]);