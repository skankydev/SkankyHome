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

namespace SkankyDev\Command;

use SkankyDev\Command\MasterCommand;

class CrudMaker extends MasterCommand
{
	
	static protected string $signature = 'crud-maker';
	static protected string $help = 'Crée les differante class et fichier pour fair un crud complet';


	private $fields = [];

	function __construct(){
		
	}

	function run(array $arg = []) :void{
		//string $name
		$this->info('CrudMaker');
		$this->array($arg);
		'ArgParser';
	}
}