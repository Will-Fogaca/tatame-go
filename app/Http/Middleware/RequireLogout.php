<?php 

namespace App\Http\Middleware;

use App\Session\LoginSession;

class RequireLogout{

    /**
     * Método responsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){       

        if(LoginSession::isLogged()){
            $route = '';
            if(LoginSession::isAdmin()){
                $route = '/admin/index';
            }

            if (LoginSession::isMaster()){
                $route = '/master/index';
            }

            if (LoginSession::isUser()){
                $route = '/user/index';
            }


            $request->getRouter()->redirect($route);
        }
        
        return $next($request);
    }
}