<?php 

    use App\Controllers\Admin\WallPostController;
    use \App\Http\Response;

    $router->get('/admin/mural', [
        function($request){
            return new Response(200, WallPostController::getIndex($request));
        }
    ]);

    $router->get('/admin/mural/cadastrar', [
        function($request){
            return new Response(200, WallPostController::getCreate($request));
        }
    ]);

    $router->post('/admin/mural/cadastrar', [
        function($request){
            return new Response(200, WallPostController::postCreate($request));
        }
    ]);

    $router->get('/admin/mural/editar', [
        function($request){
            return new Response(200, WallPostController::getUpdate($request));
        }
    ]);

    $router->post('/admin/mural/editar', [
        function($request){
            return new Response(200, WallPostController::postUpdate($request));
        }
    ]);

    $router->post('/admin/mural/excluir', [
        function($request){
            return new Response(200, WallPostController::postDelete($request));
        }
    ]);