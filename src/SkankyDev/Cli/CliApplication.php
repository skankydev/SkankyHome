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

namespace SkankyDev\Cli;

use SkankyDev\Cli\Tools\ArgParser;
use SkankyDev\Config\Config;
use SkankyDev\Core\MasterFactory;
use SkankyDev\Utilities\Traits\CliMessage;
use SkankyDev\Utilities\Traits\StringFacility;

class CliApplication {

	use CliMessage, StringFacility;

	private array $arg = [];
	private array $commandsInfo = [];

	public function __construct(array $info){
		Config::initConf();
		$this->arg = ArgParser::_pars($info);
		$this->run();

	}

	public function run(){
		$this->autoRegister();
		if(!isset($this->arg['command']) || $this->arg['command'] == 'help'){
			$this->help();
			exit;
		}
		$target = $this->findCommande($this->arg['command']);

		if(!$target){
			$this->error('Command Not Found');
			exit;
		}
		$arg = $this->arg;
		unset($arg['command']);
		$command = MasterFactory::_make($target['class']);
		return $command->run($arg);
	}



	public function autoRegister(){
		$modules = Config::get('Module');
		$modules[] = 'SkankyDev';

		foreach ($modules as $key => $module) {
			$dir = SRC_FOLDER.DS.$module.DS.'Command';
			$files = scandir($dir);
			foreach($files as $file){
				if(in_array($file, ['.','..','MasterCommand.php'])){
					continue;
				}
				$name = str_replace('.php','',$file);
				$className = $module.'\\Command\\'.$name;
				if(class_exists($className)){
					$info = $className::getInfo();
					$this->commandsInfo[$name] = $info;
				}
			}
		}
		//$this->array($this->commandClass);
	}

	public function findCommande(string $signature){
		foreach ($this->commandsInfo as $name => $command) {
			if($signature == $command['signature']){
				return $command;
			}
		}
		return null;

	}

	public function help(){
		$this->info("");
		$this->success("╭──────────────────────────────────────────────────────╮");
		$this->success("│    _____ __ __            __             __          │");
		$this->success("│   / ___// //_/___ _____  / /____  ______/ /__ _   __ │");
		$this->success("│   \__ \/ ,< / __ `/ __ \/ //_/ / / / __  / _ \ | / / │");
		$this->success("│  ___/ / /| / /_/ / / / / ,< / /_/ / /_/ /  __/ |/ /  │");
		$this->success("│ /____/_/ |_\__,_/_/ /_/_/|_|\__, /\__,_/\___/|___/   │");
		$this->success("│                            /____/                    │");
		$this->success("│                                                      │");
		$this->success("╰──────────────────────────────────────────────────────╯");
		$this->text("");
		$this->text(bleu('SkankyDev').' version '.jaune(Config::get('skankydev.version')));
		$this->text("");
		$this->warning("Usage:");
		$this->text("");
		$this->text("\tcommand [options] [arguments]");
		$this->text("");
		$this->text(jaune('Available commands:'));
		$this->text("");


		foreach ($this->commandsInfo as $name => $cmd) {
			$this->cmdHelp($cmd['signature'],$cmd['help']);
		}
		$this->text("");
		$this->text("");
	}

	public function cmdHelp(string $signature, string $help){
		echo '  '.vert(str_pad($signature, 20)) . str_pad($help, 40)."\n";
	}
}