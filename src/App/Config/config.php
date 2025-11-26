<?php 

use App\Middlewares\BiduleMiddleware;
use App\Middlewares\TrucMiddleware;

return [
	'class' => [
		'middleware' => [
			'truc' => TrucMiddleware::class,
			'bidule' => BiduleMiddleware::class,
		],
	],
	'middlewares' => [
		'truc',
	]
];