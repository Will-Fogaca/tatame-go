<?php
    use \App\Http\Response;
    use \App\Controllers\Pages\HomeController;
    use \App\Controllers\Pages\LoginController;
    use \App\Http\Router;
    $router->get('/', [
        function($request) { 
            return new Response(200, HomeController::getIndex());
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
        function ($request){
            return new Response(200, LoginController::postLogout($request));
        }
  
    ]);

    $router->get('/register', [
        'middlewares' => ['required-logout'],
        function($request){
            return new Response(200, LoginController::getRegister($request));
        }
    ]);

    $router->post('/register', [
        'middlewares' => ['required-logout'],
        function($request){
            return new Response(200, LoginController::postRegister($request));
        }
    ]);

    $router->get('/logout-force', [
        function($request) {
            session_start();
            session_destroy();
            return new Response(200, '<a href="/tatamego/login">ir para login</a>');
        }
    ]);

