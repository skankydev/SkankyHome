<?php 
/**
 * congratulation you have found the master configuration file
 */

$smtp = require_once 'smtp.config.php';
$leds = require_once 'leds.config.php';
$icons = require_once 'icons.config.php';

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
	'mqtt' => [
		'host' => 'skankyhome.local',
		'port' => 1883,
		'username'=>'',
		'password'=>'',
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
	'timehelper'=> [
		'timezone'=>'Europe/Paris'
	],
	'smtp' => $smtp,
	'debug' => 2,
	'adminMail' => 'skankydev@gmail.com',
	'leds' => $leds,
	'icons' => $icons,
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

 */