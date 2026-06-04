<?php 

use \App\Controllers\Admin\HomeController;
use \App\Http\Response;

    $router->get('/admin', [
        'middlewares'=> ['isAdmin'],
        function($request){
            return new Response(200, HomeController::getIndex($request));
        }
    ]);



