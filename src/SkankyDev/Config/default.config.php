<?php 
/**
 * Copyright (c) 2015 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

return [
	'default' => [
		'namespace' => 'App',
		'action'    => 'index'
	],
	'paginator'=>[
		'limit' => 10,
		'page'  => 1,
		'count' => 1,
		'range' => 1,
	],
	'historique' => ['limit'=>10],
	'autoloader' => [
		'Entity'     => 'Model',
		'Collection' => 'Model',
		'Behavior'   => 'Model',
	],
	'skankydev'  => [
		'version'=>'Alpha.0.0.1'
	],
	'debug' => false,
	'class' => [
		'helper' => [
			'Flash' => 'SkankyDev\View\Helper\FlashMessagesHelper',
			'Form'  => 'SkankyDev\View\Helper\FormHelper',
			'Auth'  => 'SkankyDev\View\Helper\AuthHelper',
			'Time'  => 'SkankyDev\View\Helper\TimeHelper',
		],
		'behavior' => [
			'Timed' => 'SkankyDev\Model\Behavior\TimedBehavior',
		],
		'tools' => [
			'Flash' => 'SkankyDev\Controller\Tools\FlashMessagesTool',
		],
		'listener' => [ 
			'Debug' => 'SkankyDev\Listener\DebugListener',
		]
	],
	'timehelper'=> [
		'format'=>'Y-m-d H:i:s',
		'timezone'=>'UTC'
	],
];
