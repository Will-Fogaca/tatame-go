<?php

use \App\Controllers\Admin\AbsenceReportController;
use \App\Http\Response;

$router->get('/admin/faltas', [
    'middlewares'=> ['isAdmin'],
    function($request){
        return new Response(200, AbsenceReportController::getIndex($request));
    }
]);