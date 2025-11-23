<?php
require '../vendor/autoload.php';

header('X-Frame-Options: DENY');//Clickjacking protection

if ( !defined('DS') ){
	define('DS', DIRECTORY_SEPARATOR);
}

define('PUBLIC_FOLDER',dirname(__FILE__));//dossier public
define('APP_FOLDER', dirname(PUBLIC_FOLDER));//dossier de l'application

mb_internal_encoding("UTF-8");	//defini encodage des carataire utf-8

$app = new SkankyDev\Core\Application();
$app->run();