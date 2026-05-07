<?php 
    use \App\Controllers\Admin\BeltRankController;
    use \App\Http\Response;


    $router->get('/admin/graduacao', [
        function($request){
            return new Response(200, BeltRankController::getIndex($request));
        }
    ]);

    $router->get('/admin/graduacao/cadastrar', [
        function($request){
            return new Response(200, BeltRankController::getCreate($request));
        }
    ]);

    $router->post('/admin/graduacao/cadastrar', [
        function($request){
            return new Response(200, BeltRankController::postCreate($request));
        }
    ]);

