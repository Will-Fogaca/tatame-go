<?php

namespace App\Http\Middleware;

use App\Session\LoginSession;
use App\Http\Request;

class AccessControl {

    /**
     * Método responsável por executar o middleware
     */
    public function handle($request, $next){

        $path = parse_url($request->getRouter()->getCurrentUrl(), PHP_URL_PATH);
      
        if(!LoginSession::isLogged()){
            if($path !== '/'){
                $request->getRouter()->redirect('/');
                return;
            }

            return $next($request);
        }

        $user = LoginSession::getUser();
        $type = $user['user_type'] ?? 'user';

        $allowedPrefix = "/$type";

        if(str_starts_with($path, $allowedPrefix)){
            return $next($request);
        }

        // fallback seguro
        $request->getRouter()->redirect("/$type");
        return;
    }
}