<?php 

use App\Controller\HomeController;
use SkankyDev\Http\Routing\Router;

Router::_add('/',[
	'controller' => 'Home',
	'action'     => 'index',
	'namespace'  => 'App'
]);

