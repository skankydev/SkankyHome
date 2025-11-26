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
namespace SkankyDev\Utilities\Traits;


trait StringFacility {

	/**
	 * convert infoActionName to info-action-name
	 * @param  string $string the string need to be convert
	 * @return string         the result
	 */
	public function toDash($string,$delimiters = '-'){
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
	public function dotToFolder($string, $charset='utf-8'){
		$list = explode('.', $string);
		$string = implode(DS,$list);
		return $string;
	}
}