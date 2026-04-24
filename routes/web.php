<?php
    use \App\Http\Response;
    use \App\Controllers\Pages\HomeController;
    use \App\Controllers\Pages\LoginController;
    use \App\Http\Router;
    $router->get('/', [
        function($request) { 
            return new Response(200, HomeController::getHome());
        }
    ]);

    $router->get('/login', [
        'middlewares' => ['required-logout'],
        function($request){
        return new Response(200, LoginController::getIndex($request));
        }
    ]);

    $router->post('/login', [
        'middlewares' => ['required-logout'],
        function($request){
        return new Response(200, LoginController::postLogin($request));
        }
    ]);

    $router->post('/logout', [
        'middlewares' => ['required-login'],
        function ($request){
            return new Response(200, LoginController::postLogout($request));
        }
  
    ]);

    $router->get('/register', [
        'middleware' => ['required-logout'],
        function($request){
            return new Response(200, LoginController::getRegister($request));
        }
    ]);

