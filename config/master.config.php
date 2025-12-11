<?php 
/**
 * congratulation you have found the master configuration file
 */

$smtp = require_once 'smtp.config.php';
$leds = require_once 'leds.config.php';

$conf =  [
	'db' => [
		'MongoDB' =>[
			'host'=>'localhost',
			'port'=>'27017',
			'username'=>'',
			'password'=>'',
			'database'=>'SkankyHome'
		]
	],
	'location'=>[
		'fr'=>[
			'domaine'=>'App',
			'langue' =>'fr_FR'
		]
	],
	'Module'=>[
		'App'
	],
	'smtp' => $smtp,
	'debug' => 2,
	'adminMail' => 'skankydev@gmail.com',
	'leds' => $leds,
];
return $conf;

/*
smtp.config.php exemple
return [
	'default' => [
		'host' => '***',
		'username' => '***',
		'password' => '***',
		'secure' => 'ssl',
		'port' => '465',
		'sender' => 'no-reply@mail.com'
	]
];

smtp.config.php exemple
return [
	
];

 */