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

namespace SkankyDev\Utilities\Traits;

trait CliMessage
{
	
	function info($message){
		echo "\033[36m$message \033[0m\n";
	}
	function infoT($message){
		$this->info(" \t$message");
	}

	function error($str){
		echo "\033[31m$str \033[0m\n";
	}

	function success($str){
		echo "\033[32m$str \033[0m\n";
	}

	function warning($str){
		echo "\033[33m$str \033[0m\n";
	}

	function text($str){
		echo "$str \n";
	}

	function line(){
		$this->info('────────────────────────────────');
	}

	function choice($choice){
		$this->line();
		foreach($choice as $k => $v){
			$this->info($k.' : '.$v);
		}
		return readline(' ? ');
	}

	function ask($str){
		return readline($str.' ? ');
	}

	function valide($str){
		return readline($str.' ? (y/n) ');
	}

	function array(array $data, string $prefix = "  ", bool $isFirstLevel = true) {
		if ($isFirstLevel) {
			echo ("[\n");
		}

		$lastKey = array_key_last($data);
		foreach ($data as $key => $value) {
			echo $prefix.$prefix;

			// Affiche la clé en jaune vif
			if (is_int($key)) {
				echo bleu("[$key]") . " => ";
			} else {
				echo bleu($key) . " => ";
			}

			if (is_array($value)) {
				echo "[\n";
				$this->array($value, $prefix . "  ", false);
			} elseif (is_object($value)) {
				// Gestion des objets
				echo cyanVif("object(") . vertVif(get_class($value)) . cyanVif(")");
			} elseif (is_bool($value)) {
				// Gestion des booléens
				echo $value ? bleuVif('true') : rouge('false');
			} elseif (is_null($value)) {
				// Gestion des null
				echo orange('null');
			} elseif (is_string($value)) {
				// Gestion des chaînes de caractères
				echo vert('"' . ($value) . '"');
			} else {
				// Gestion des autres types (nombres, etc.)
				echo vertVif($value);
			}

			// Ajoute une virgule sauf pour le dernier élément
			if ($key !== $lastKey) {
				echo ",";
			}

			echo "\n";
		}

		if ($isFirstLevel) {
			echo ("]\n");
		} else {
			echo $prefix . ("]");
		}
	}

}
