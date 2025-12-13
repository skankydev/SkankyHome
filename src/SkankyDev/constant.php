<?php 
/**
 * Copyright (c) 2025 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */


define('APP_FOLDER', dirname(dirname(__DIR__)));

const TIME_SECOND = 1;
const TIME_MINUTE = 60;
const TIME_HOUR   = 3600;
const TIME_DAY    = 86400;
const TIME_WEEK   = 604800;
const TIME_MONTH  = 2592000;
const TIME_YEAR   = 31536000;

const DS = DIRECTORY_SEPARATOR;
const PUBLIC_FOLDER = APP_FOLDER.DS.'public';
const VIEW_FOLDER = APP_FOLDER.DS.'src_front'.DS.'view';
const SRC_FOLDER = APP_FOLDER.DS.'src';


