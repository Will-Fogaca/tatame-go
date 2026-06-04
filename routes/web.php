<?php

use App\Http\Response;
use App\Controllers\Pages\HomeController;
use App\Controllers\Pages\LoginController;

$router->get('/', [
    function ($request) {
        return new Response(200, HomeController::getIndex());
    }
]);

// LOGIN (somente se NÃO estiver logado)
$router->get('/login', [
    'middlewares' => ['required-logout'],
    function ($request) {
        return new Response(200, LoginController::getIndex($request));
    }
]);

$router->post('/login', [
    'middlewares' => ['required-logout'],
    function ($request) {
        return new Response(200, LoginController::postLogin($request));
    }
]);


$router->get('/registrar', [
    'middlewares' => ['required-logout'],
    function ($request) {
        return new Response(200, LoginController::getRegister($request));
    }
]);

$router->post('/registrar', [
    'middlewares' => ['required-logout'],
    function ($request) {
        return new Response(200, LoginController::postRegister($request));
    }
]);

// LOGOUT (somente logado)
$router->post('/logout', [
    'middlewares' => ['required-login'],
    function ($request) {
        return new Response(200, LoginController::postLogout($request));
    }
]);

// DEBUG logout force (opcional)
$router->get('/logout-force', [
    function ($request) {
        session_start();
        session_destroy();

        return new Response(200, '<a href="/login">ir para login</a>');
    }
]);