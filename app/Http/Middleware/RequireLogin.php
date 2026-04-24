<?php 

namespace App\Http\Middleware;

use App\Session\LoginSession;

class RequireLogin{

    /**
     * Método responsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){       

        if(!LoginSession::isLogged()){

            $request->getRouter()->redirect('/login');
        }
        
        return $next($request);
    }
}