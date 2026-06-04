<?php

use \App\Controllers\User\AbsenceController;
use \App\Http\Response;

$router->get('/presencas', [
    'middlewares' => ['required-login'],
    function($request){
        return new Response(200, AbsenceController::getIndex($request));
    }
]);