<?php 

use App\Controller\HomeController;
use SkankyDev\Http\Routing\Router;
use App\Middlewares\BiduleMiddleware;
use App\Middlewares\TrucMiddleware;


Router::_add('/',[
	'controller' => 'Home',
	'action'     => 'index',
	'namespace'  => 'App'
])->setMiddlewares(['bidule']);

/*Router::_add('/sequences/:sequence', [
	'controller' => 'Sequence',
	'action' => 'show',
	'namespace'  => 'App'
],[
	'sequence'=> '[a-z0-9]*'
]);*/

