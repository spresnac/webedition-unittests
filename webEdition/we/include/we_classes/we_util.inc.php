<?php

/**
 * webEdition CMS
 *
 * $Rev: 5872 $
 * $Author: mokraemer $
 * $Date: 2013-02-23 12:01:09 +0100 (Sat, 23 Feb 2013) $
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

/**
 * Util Functions
 *
 * all functions in this class are static! Please use it in static form:
 *    we_util::function_name();
 *
 *
 * @static
 */
abstract class we_util{

	/**
	 * Formates a number with a country specific format into computer readable format.
	 * Returns the formated number.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function std_numberformat($number){
		if(strpos($number, 'E')){ //  when number is too big, it is shown with E+xx
			$number = number_format($number, 2, '.', '');
		}
		if(preg_match('|.*,[0-9]*$|', $number)){ // deutsche schreibweise
			$tmp = explode(',', str_replace(".", "", $number));
			$number = implode('.', $tmp);
		} else if(preg_match('|.*\.[0-9]*$|', $number)){ // engl schreibweise
			$number = str_replace(',', '', $number);
		} else{
			$number = str_replace(array(',', '.'), '', $number);
		}
		return $number;
	}

	/**
	 * Converts all windows and mac newlines from string to unix newlines
	 * Returns the converted String.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function cleanNewLine($string){
		return str_replace(array("\n\r", "\r\n", "\r"), "\n", $string);
	}

	/**
	 * Removes from string all newlines and converts all <br> to newlines
	 * Returns the converted String.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function br2nl($string){
		$string = str_replace(array("\n", "\r"), '', $string);
		return preg_replace('|<br ?/?>|i', "\n", $string);
	}

	static function rmPhp($in){
		$out = '';
		$starttag = strpos($in, '<?');
		if($starttag === false){
			return $in;
		}
		$lastStart = 0;
		while(!($starttag === false)) {
			$endtag = strpos($in, '?>', $starttag);
			$out .= substr($in, $lastStart, ($starttag - $lastStart));
			$lastStart = $endtag + 2;
			$starttag = strpos($in, '<?', $lastStart);
		}
		if($lastStart < strlen($in)){
			$out .= substr($in, $lastStart, (strlen($in) - $lastStart));
		}
		return $out;
	}

	static function getGlobalPath(){
		return (isset($GLOBALS['WE_MAIN_DOC']) && isset($GLOBALS['WE_MAIN_DOC']->Path)?$GLOBALS['WE_MAIN_DOC']->Path:'');
	}

	static function html2uml($text){
		return html_entity_decode($text, ENT_COMPAT, (isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET'] ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET));
	}

	/**
	 * returns array of directory IDs of all directories which are located inside $folderID (recursive)
	 *
	 * @return array
	 * @param int $folderID
	 */
	static function getFoldersInFolder($folderID, $table = FILE_TABLE, $db = ''){
		$outArray = array(
			$folderID
		);
		$db = ($db ? $db : new DB_WE());
		$db->query('SELECT ID FROM ' . $table . ' WHERE ParentID=' . intval($folderID) . ' AND IsFolder=1');
		$new = array();
		while($db->next_record()) {
			$new[] = $db->f('ID');
		}
		foreach($new as $cur){
			$tmpArray = self::getFoldersInFolder($cur, $table, $db);
			$outArray = array_merge($outArray, $tmpArray);
		}
		return $outArray;
	}

}
