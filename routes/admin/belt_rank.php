<?php 
    use \App\Controllers\Admin\BeltRankController;
    use \App\Http\Response;


    $router->get('/admin/graduacao', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, BeltRankController::getIndex($request));
        }
    ]);

    $router->get('/admin/graduacao/cadastrar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, BeltRankController::getCreate($request));
        }
    ]);

    $router->post('/admin/graduacao/cadastrar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, BeltRankController::postCreate($request));
        }
    ]);
    
    $router->get('/admin/graduacao/editar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, BeltRankController::getUpdate($request)); 
        }
    ]);

    $router->post('/admin/graduacao/editar', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, BeltRankController::postUpdate($request)); 
        }
    ]);

    $router->post('/admin/graduacao/excluir', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, BeltRankController::postDelete($request));
        }
    ]);

    $router->get('/admin/graduacao/proximo', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(
                200,
                BeltRankController::getNextLevel($request),
                'application/json'
            );
        }
    ]);
