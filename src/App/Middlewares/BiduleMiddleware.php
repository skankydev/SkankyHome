<?php 

namespace App\Middlewares;

use SkankyDev\Http\Middleware\MiddlewareInterface;
use SkankyDev\Http\Request;



class BiduleMiddleware implements MiddlewareInterface{

	public function handle(Request $request,callable $next){
		//echo "AVANT Bidule\n";
        
        // Continuer vers le prochain middleware
        $response = $next($request);
        
        //echo "APRÈS Bidule\n";
        
        return $response;
	}

}