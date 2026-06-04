<?php 

    use App\Controllers\User\WallPostController;
    use \App\Http\Response;

    $router->get('/user/mural', [
        'middlewares' => ['required-login'],
        function($request){
            return new Response(200, WallPostController::getIndex($request));
        }
    ]);
