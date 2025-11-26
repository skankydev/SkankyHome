<?php   

namespace SkankyDev\Http\Middleware;

use SkankyDev\Http\Request;

interface MiddlewareInterface {
    /**
     * @param Request $request
     * @param callable $next Le prochain middleware/controller
     * @return mixed
     */
    public function handle(Request $request, callable $next);
    
}