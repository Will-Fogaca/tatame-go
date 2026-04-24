<?php 

    use \App\Controllers\Admin\AcademyController;
    use \App\Http\Response;


    $router->get('/admin/academia', [
        function($request){
            return new Response(200, AcademyController::getIndex($request));
        }
    ]);

    $router->get('/admin/academia/cadastrar', [
        function($request){
            return new Response(200, AcademyController::getCreate($request));
        }
    ]);

    $router->post('/admin/academia/cadastrar', [
        function($request){
            return new Response(200, AcademyController::postCreate($request));
        }
    ]);

    $router->get('/admin/academia/editar', [
        function($request){
            return new Response(200, AcademyController::getUpdate($request));
        }
    ]);

    $router->post('/admin/academia/editar', [
        function($request){
            return new Response(200, AcademyController::postUpdate($request));
        }
    ]);

    $router->post('/admin/academia/excluir', [
        function($request){
            return new Response(200, AcademyController::postDelete($request));
        }
    ]);
