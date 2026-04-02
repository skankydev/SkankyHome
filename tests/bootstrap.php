<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('TIME_HOUR')) {
    define('TIME_HOUR', 3600);
}

// Minimal config required by the framework components under test.
// No database, no filesystem — pure unit test context.
use SkankyDev\Config\Config;

// $conf is null by default — initialise before any set() call
Config::$conf = [];

Config::set('default.namespace', 'App');
Config::set('default.action', 'index');

Config::set('class.rules', [
    'required'   => \SkankyDev\Validation\Rules\Required::class,
    'email'      => \SkankyDev\Validation\Rules\Email::class,
    'max_length' => \SkankyDev\Validation\Rules\MaxLength::class,
    'min_length' => \SkankyDev\Validation\Rules\MinLength::class,
    'numeric'    => \SkankyDev\Validation\Rules\Numeric::class,
    'max'        => \SkankyDev\Validation\Rules\Max::class,
    'min'        => \SkankyDev\Validation\Rules\Min::class,
    'regex'      => \SkankyDev\Validation\Rules\Regex::class,
    'confirmed'  => \SkankyDev\Validation\Rules\Confirmed::class,
    'same'       => \SkankyDev\Validation\Rules\Same::class,
    'hex_color'  => \SkankyDev\Validation\Rules\HexColor::class,
]);
