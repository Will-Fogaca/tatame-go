<?php

namespace App\Http\Middleware;

use App\Session\LoginSession;
use App\Controllers\Pages\ErrorController;

class IsAdmin
{
    public function handle($request, $next)
    {
        $user = LoginSession::getUser();
       
        if (!$user || $user['user_type'] != 'admin') {
            return ErrorController::get403();
        }

        return $next($request);
    }
}