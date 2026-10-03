<?php 
/**
 * congratulation you have found the master configuration file
 */

$leds = require_once 'leds.config.php';
$icons = require_once 'icons.config.php';

$conf =  [
	'SkankyHome'=> '0.1.0',
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
	'llama' => [
		'host' => getenv('LLAMA_HOST') ?: 'localhost',
		'port' => getenv('LLAMA_PORT') ?: 8080,
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
	'debug'     => (int)(getenv('APP_DEBUG') !== false ? getenv('APP_DEBUG') : 2),
	'adminMail' => getenv('APP_ADMIN_MAIL') ?: 'skankydev@gmail.com',
	'leds' => $leds,
	'icons' => $icons,
	'view' => [
		'error' => VIEW_FOLDER.DS.'error',
		'error_layout' => VIEW_FOLDER.DS.'layout'.DS.'error.php',
		'fields' => VIEW_FOLDER.DS.'fields',
	],
	'template' => [
		'folder' => TEMPLATE_FOLDER,
	],
];

return $conf;

