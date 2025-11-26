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

/*Router::_add('/articles',[
	'controller' => 'Post',
	'action'     => 'index',
	'namespace'  => 'App'
]);*/

Router::_add('/home/show/:slug',[
	'controller' => 'Home',
	'action'     => 'show',
	'namespace'  => 'App'
],[
	'slug' => '[a-z0-9-]*'
])->setMiddlewares([BiduleMiddleware::class,TrucMiddleware::class]);