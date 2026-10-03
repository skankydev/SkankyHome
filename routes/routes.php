<?php 

use App\Controller\HomeController;
use SkankyDev\Http\Routing\Router;

Router::_add('/',[
	'controller' => HomeController::class,
	'action'     => 'index',
])->setName('home');

