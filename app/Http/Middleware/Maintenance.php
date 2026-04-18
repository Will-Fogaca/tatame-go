<?php 

namespace App\Http\Middleware;

class Maintenance{
    /**
     * Método responsável por executar o middlware
     *
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle($request, $next){       
        if(getenv('MAINTENANCE') === 'true'){
             throw new \Exception("Página em manutenção. Tente novamente mais tarde.", 200);
        }
        return $next($request);
    }
}