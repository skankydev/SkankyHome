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

namespace App\Command;

use SkankyDev\Command\MasterCommand;


class MqttLoop extends MasterCommand {

	static protected string $signature = 'mqtt-loop';
	static protected string $help = 'ca viendra un jour mais je sais pas quand';


	public function run(array $arg = []): void {
		$this->info('Youpi');
	}

}