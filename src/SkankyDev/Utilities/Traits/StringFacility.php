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


trait StringFacility {



	/**
	 * convert infoActionName to info-action-name
	 * @param  string $string the string need to be convert
	 * @return string         the result
	 */
	public function toDash($string,$delimiters = '-'){
		$string = lcfirst($string);
		$string = preg_replace('/[A-Z]/',$delimiters."$0",$string);
		$string = strtolower($string);
		return trim($string,' -');
	}

	/**
	 * convert info-action-name to InfoActionName
	 * @param  string $string the string need to be convert
	 * @return string         the result
	 */
	public function toCap($string, $delimiters = '-'){
		$string = str_replace($delimiters, ' ', $string);
		$string = ucwords($string);
		$string = str_replace(' ', '', $string);
		return trim($string);
	}

	/**
	 * convert info-action-name to infoActionName
	 * @param  string $string the string need to be convert
	 * @return string         the result
	 */
	public function toCamel($string, $delimiters = '-'){
		//$string = str_replace('-', ' ', $string);
		$string = lcfirst(ucwords($string, $delimiters));
		$string = str_replace($delimiters, '', $string);
		return trim($string);
	}

	/**
	 * remove all weird characters
	 * @param  string $string  the string need to be clean
	 * @param  string $charset the charset
	 * @return string          the result
	 */
	public function cleanString($string, $charset='utf-8'){
		$string = htmlentities($string, ENT_NOQUOTES, $charset);
		
		$string = preg_replace('/&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);/', '\1', $string);
		$string = preg_replace('/&([A-za-z]{2})(?:lig);/', '\1', $string); 
		$string = preg_replace('/&[^;]+;/', '', $string);
		$string = str_replace(' ', '', $string);
		
		return $string;
	}

	/**
	 * convert info-action-name to infoActionName
	 * @param  string $string the string need to be convert
	 * @return string         the result
	 */
	 function dotToFolder($string, $charset='utf-8'){
		$list = explode('.', $string);
		$string = implode(DS,$list);
		return $string;
	}


	public function pluralize(string $word): string {
		// Règles simples de pluriel
		if (str_ends_with($word, 'y')) {
			return substr($word, 0, -1) . 'ies';
		}
		if (str_ends_with($word, 's') || str_ends_with($word, 'x')) {
			return $word . 'es';
		}
		return $word . 's';
	}

	public function singularize(string $word): string {
		// Cas spéciaux (irréguliers)
		$irregulars = [
			'children' => 'child',
			'people' => 'person',
			'men' => 'man',
			'women' => 'woman',
			'teeth' => 'tooth',
			'feet' => 'foot',
			'mice' => 'mouse',
			'geese' => 'goose',
		];
		
		$lower = strtolower($word);
		if (isset($irregulars[$lower])) {
			// Préserver la casse originale
			return $this->preserveCase($word, $irregulars[$lower]);
		}
		
		// Règles de singularisation
		$rules = [
			'/ies$/i' => 'y',
			'/ves$/i' => 'f',
			'/ses$/i' => 's',
			'/xes$/i' => 'x',
			'/ches$/i' => 'ch', 
			'/shes$/i' => 'sh',
			'/s$/i' => '', 
		];
		
		foreach ($rules as $pattern => $replacement) {
			if (preg_match($pattern, $word)) {
				return preg_replace($pattern, $replacement, $word);
			}
		}
		
		return $word;
	}

	public function preserveCase(string $original, string $replacement): string {
		// Si le mot original commence par une majuscule
		if (ctype_upper($original[0])) {
			return ucfirst($replacement);
		}
		return $replacement;
	}

	public function toHuman(string $string){
		$string = str_replace('-', ' ', $string);
		$string = str_replace('_', ' ', $string);
		$string = ucwords($string);
		return $string;
	}
}