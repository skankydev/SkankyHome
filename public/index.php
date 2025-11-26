<?php
require '../vendor/autoload.php';

header('X-Frame-Options: DENY'); //Clickjacking protection
mb_internal_encoding("UTF-8");	 //defini encodage des carataire utf-8

$app = new SkankyDev\Core\Application();
$app->run();