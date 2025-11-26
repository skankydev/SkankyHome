<?php 

namespace App\Middlewares;

use SkankyDev\Http\Middleware\MiddlewareInterface;
use SkankyDev\Http\Request;


class TrucMiddleware implements MiddlewareInterface{

	public function handle(Request $request,callable $next){
		//echo "AVANT Truc\n";
        
        // Continuer vers le prochain middleware
        $response = $next($request);
        
        //echo "APRÈS Truc\n";
        
        return $response;
	}

}