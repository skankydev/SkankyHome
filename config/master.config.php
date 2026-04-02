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
			'host'     => getenv('DB_MONGO_HOST')     ?: 'localhost',
			'port'     => getenv('DB_MONGO_PORT')     ?: '27017',
			'username' => getenv('DB_MONGO_USERNAME') ?: '',
			'password' => getenv('DB_MONGO_PASSWORD') ?: '',
			'database' => getenv('DB_MONGO_DATABASE') ?: 'SkankyHome',
		]
	],
	'mqtt' => [
		'host' =>getenv('MQTT_HOST') ?: 'skankyhome.local',
		'port' =>getenv('MQTT_PORT') ?: 1883,
		'username'=>getenv('MQTT_USERNAME') ?: '',
		'password'=>getenv('MQTT_PASSWORD') ?: '',
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
	'debug'     => (int)(getenv('APP_DEBUG') !== false ? getenv('APP_DEBUG') : 2),
	'adminMail' => getenv('APP_ADMIN_MAIL') ?: 'skankydev@gmail.com',
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