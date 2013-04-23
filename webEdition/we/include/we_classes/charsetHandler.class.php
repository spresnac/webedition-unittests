<?php

/**
 * webEdition CMS
 *
 * $Rev: 5601 $
 * $Author: mokraemer $
 * $Date: 2013-01-20 19:16:46 +0100 (Sun, 20 Jan 2013) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class charsetHandler{

	var $charsets = array();

	/**
	 * @return charsetHandler
	 * initialises with all available charsets
	 */
	function __construct(){
		//	First ISO-8859-charsets
		$this->charsets = array(
			'west_european' => array(
				'national' => 'West Europe', //	Here is the name of the country in mother language
				'charset' => 'ISO-8859-1',
				'international' => g_l('charset', '[titles][west_european]'), //	Name in selected language
			),
			'central_european' => array(
				'national' => 'Central Europe',
				'charset' => 'ISO-8859-2',
				'international' => g_l('charset', '[titles][central_european]'),
			),
			'south_european' => array(
				'national' => 'South Europe',
				'charset' => 'ISO-8859-3',
				'international' => g_l('charset', '[titles][south_european]'),
			),
			'north_european' => array('national' => 'North Europe',
				'charset' => 'ISO-8859-4',
				'international' => g_l('charset', '[titles][north_european]'),
			),
			'cyrillic' => array(
				'national' => ',&#1077,&#1086,&#1073,&#1077,&#1089,&#1087,&#1077,&#1095,',
				'charset' => 'ISO-8859-5',
				'international' => g_l('charset', '[titles][cyrillic]'),
			),
			'arabic' => array(
				'national' => '&#1578,&#1587,&#1580,&#1617,&#1604, &#1575,&#1604,&#1570,&#1606,',
				'charset' => 'ISO-8859-6',
				'international' => g_l('charset', '[titles][arabic]'),
			),
			'greek' => array(
				'national' => 'Greek',
				'charset' => 'ISO-8859-7',
				'international' => g_l('charset', '[titles][greek]'),
			),
			'hebrew' => array(
				'national' => '&#1488,&#1497,&#1512,&#1493,&#1508,&#1492,',
				'charset' => 'ISO-8859-8',
				'international' => g_l('charset', '[titles][hebrew]'),
			),
			'turkish' => array(
				'national' => 'Turkish',
				'charset' => 'ISO-8859-9',
				'international' => g_l('charset', '[titles][turkish]'),
			),
			'nordic' => array(
				'national' => 'Nordic',
				'charset' => 'ISO-8859-10',
				'international' => g_l('charset', '[titles][nordic]'),
			),
			'thai' => array(
				'national' => 'Thai',
				'charset' => 'ISO-8859-11',
				'international' => g_l('charset', '[titles][thai]'),
			),
			'baltic' => array(
				'national' => 'baltic',
				'charset' => 'ISO-8859-13',
				'international' => g_l('charset', '[titles][baltic]'),
			),
			'keltic' => array(
				'national' => 'keltic',
				'charset' => 'ISO-8859-14',
				'international' => g_l('charset', '[titles][keltic]'),
			),
			'extended_european' => array(
				'national' => 'ISO-8859-15',
				'charset' => 'ISO-8859-15',
				'international' => g_l('charset', '[titles][extended_european]'),
			),
			'unicode' => array(
				'national' => 'Unicode',
				'charset' => 'UTF-8',
				'international' => g_l('charset', '[titles][unicode]'),
			),
			'windows_1251' => array(
				'national' => 'Windows-1251',
				'charset' => 'Windows-1251',
				'international' => g_l('charset', '[titles][windows_1251]'),
			),
			'windows_1252' => array(
				'national' => 'Windows-1252',
				'charset' => 'Windows-1252',
				'international' => g_l('charset', '[titles][windows_1252]'),
			),
		);
	}

	/**
	 * @return array
	 * @param $availableChars array
	 * @desc This function returns an array(key = charset / value = charset - name(international) (name(national)))
	 */
	function getCharsetsForTagWizzard(){
		$retArr = array();
		foreach($this->charsets as $val){

			$retArr[$val['charset']] = $val['charset'] . ' - ' . $val['international'] . ' (' . $val['national'] . ')';
		}
		return $retArr;
	}

	/**
	 * @return array
	 * @param string $charset
	 * @desc returns array (national, international, charset, when charset is known)
	 */
	function getCharsetArrByCharset($charset){
		$charset=strtolower($charset);
		foreach($this->charsets as $key => $val){

			if(strtolower($val['charset']) == $charset){
				return $this->charsets[$key];
			}
		}
		return false;
	}

	/**
	 * @return array
	 * @param $availableChars array
	 * @desc This function returns an array for the property page of a webEdition document
	 */
	function getCharsetsByArray($availableChars){
		$tmpCharArray = array();
		$retArr = array();

		foreach($availableChars as $char){
			$tmpCharArray[] = (($charset = $this->getCharsetArrByCharset($char)) ? $charset : array('charset' => $char));
		}

		foreach($tmpCharArray as $val){
			$retArr[$val['charset']] = $val['charset'] .
				(isset($val['international']) ? ' - ' . $val['international'] . ' (' . $val['national'] . ')' : '');
		}

		return $retArr;
	}

	//FIXME: use array obove; currently this seems to complecated
	static function getAvailCharsets(){
		return array(
			'UTF-8',
			'ISO-8859-1',
			'ISO-8859-2',
			'ISO-8859-3',
			'ISO-8859-4',
			'ISO-8859-5',
			'ISO-8859-6',
			'ISO-8859-7',
			'ISO-8859-8',
			'ISO-8859-9',
			'ISO-8859-10',
			'ISO-8859-11',
			'ISO-8859-12',
			'ISO-8859-13',
			'ISO-8859-14',
			'ISO-8859-15',
			'Windows-1251',
			'Windows-1252',
		);
	}

}
