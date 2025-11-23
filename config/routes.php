<?php 

use App\Controller\HomeController;
use SkankyDev\Http\Routing\Router;



Router::_add('/',[
	'controller' => HomeController::class,
	'action'     => 'index',
	'namespace'  => 'App'
]);

/*Router::_add('/articles',[
	'controller' => 'Post',
	'action'     => 'index',
	'namespace'  => 'App'
]);*/

Router::_add('/home/show/:slug',[
	'controller' => HomeController::class,
	'action'     => 'show',
	'namespace'  => 'App'
],[
	'slug' => '[a-z0-9-]*'
]);