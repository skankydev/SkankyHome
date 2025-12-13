<?php 
/**
 * Copyright (c) 2025 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

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