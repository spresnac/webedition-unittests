<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
abstract class importFunctions{

	/**
	 * @return boolean
	 * @param integer $parentID
	 * @param integer $templateID
	 * @param array $fields
	 * @param integer $doctypeID
	 * @param string $categories
	 * @param string $filename
	 * @param boolean $isDynamic
	 * @param string $extension
	 * @param boolean $publish
	 * @param boolean $IsSearchable
	 * @desc imports a document into webedition
	 */
	static function importDocument($parentID, $templateID, $fields, $doctypeID = 0, $categories = "", $filename = "", $isDynamic = true, $extension = ".php", $publish = true, $IsSearchable = true, $conflict = 'rename'){

		// erzeugen eines neuen webEdition-Dokument-Objekts
		$GLOBALS['we_doc'] = new we_webEditionDocument();

		$GLOBALS['we_doc']->we_new();

		$GLOBALS['we_doc']->Extension = $extension;
		if($filename){
			$filename = self::correctFilename($filename);
			$GLOBALS['we_doc']->Filename = $filename;
		}
		$GLOBALS['we_doc']->Text = $GLOBALS['we_doc']->Filename . $GLOBALS['we_doc']->Extension;

		$GLOBALS['we_doc']->setParentID($parentID);
		$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->getParentPath() . (($GLOBALS['we_doc']->getParentPath() != "/") ? "/" : "") . $GLOBALS['we_doc']->Text;
		// IF NAME OF OBJECT EXISTS, WE HAVE TO CREATE A NEW NAME
		if(($file_id = f("SELECT ID FROM " . FILE_TABLE . " WHERE Path='" . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->Path) . "'", "ID", $GLOBALS['DB_WE']))){
			if($conflict == 'rename'){
				$z = 0;
				$footext = $GLOBALS['we_doc']->Filename . "_" . $z . $GLOBALS['we_doc']->Extension;
				while(f("SELECT ID FROM " . FILE_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($footext) . "' AND ParentID=" . intval($GLOBALS['we_doc']->ParentID), "ID", $GLOBALS['DB_WE'])) {
					$z++;
					$footext = $GLOBALS['we_doc']->Filename . "_" . $z . $GLOBALS['we_doc']->Extension;
				}
				$GLOBALS['we_doc']->Filename = $GLOBALS['we_doc']->Filename . "_" . $z;

				$GLOBALS['we_doc']->Text = $footext;
				$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->getParentPath() . (($GLOBALS['we_doc']->getParentPath() != "/") ? "/" : "") . $GLOBALS['we_doc']->Text;
			} else if($conflict == 'replace'){
				$GLOBALS['we_doc']->initById($file_id);
			} else{
				return true;
			}
		}

		$GLOBALS['we_doc']->DocType = $doctypeID;
		$GLOBALS['we_doc']->setTemplateID($templateID);
		$GLOBALS['we_doc']->Category = $categories;

		$GLOBALS['we_doc']->ContentType = "text/webedition";

		$GLOBALS['we_doc']->IsDynamic = $isDynamic;
		$GLOBALS['we_doc']->IsSearchable = $IsSearchable;
		foreach($fields as $fieldName => $fieldValue){
			$GLOBALS['we_doc']->setElement($fieldName, $fieldValue);
		}

		// SAVE DOCUMENT
		if(!$GLOBALS['we_doc']->we_save()){
			return false;
		}
		// PUBLISH OR EXIT
		if($publish){
			return $GLOBALS['we_doc']->we_publish();
		} else{
			return true;
		}
	}

	/**
	 * @return boolean
	 * @param integer $classID
	 * @param array $fields
	 * @param string $categories
	 * @param string $filename
	 * @param boolean $publish
	 * @desc imports an object into webEdition
	 */
	static function importObject($classID, $fields, $categories = "", $filename = "", $publish = true, $conflict = 'rename'){

		// INIT OBJECT
		$object = new we_objectFile();
		$object->we_new();
		$object->TableID = $classID;
		$object->setRootDirID(true);
		$object->resetParentID();
		$object->restoreDefaults();
		if($categories){
			$object->Category = $categories;
		}

		// IF WE HAVE TO GIVE THE OBJECT A NAME
		if($filename || $filename == 0){
			$name_exists = false;
			$filename = importFunctions::correctFilename($filename);
			$object->Text = $filename;
			$object->Path = $object->getParentPath() . (($object->getParentPath() != "/") ? "/" : "") . $object->Text;
			// IF NAME OF OBJECT EXISTS, WE HAVE TO CREATE A NEW NAME
			if($file_id = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='" . $GLOBALS['DB_WE']->escape($object->Path) . "'", "ID", $GLOBALS['DB_WE'])){
				$name_exists = true;
				if($conflict == 'replace'){
					$object->initByID($file_id, OBJECT_FILES_TABLE);
				} else if($conflict == 'rename'){
					$z = 0;
					$footext = $object->Text . "_" . $z;
					while(f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($footext) . "' AND ParentID=" . intval($object->ParentID), "ID", $GLOBALS['DB_WE'])) {
						$z++;
						$footext = $object->Text . "_" . $z;
					}
					$object->Text = $footext;
					$object->Path = $object->getParentPath() . (($object->getParentPath() != "/") ? "/" : "") . $object->Text;
				} else{
					return true;
				}
			}
		}

		// FILL FIELDS OF OBJECT
		foreach($fields as $fieldName => $fieldValue){
			$object->setElement($fieldName, $fieldValue);
		}
		// SAVE OBJECT
		if(!$object->we_save()){
			return false;
		}
		// PUBLISH OR EXIT
		if($publish){
			return $object->we_publish();
		} else{
			return true;
		}
	}

	/**
	 * @return string
	 * @param string $filename
	 * @desc corrects the filename if it contains invalid chars
	 */
	static function correctFilename($filename, $allowPath = false){
		$filename = str_replace(array(' ', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'), array('-', 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'), $filename);
		$filename = preg_replace('%[^a-z0-9\._+-' . ($allowPath ? '/' : '') . ']%i', '', $filename);
		if(strlen($filename) > 100){
			$filename = substr($filename, 0, 100);
		}
		return strlen($filename) ? trim($filename, '/') : "newfile";
	}

	/**
	 * @return int
	 * @param string $datestring
	 * @param string $format
	 * @desc converts a $datestring which represent a date to an unix timestamp with the given $format. If $format is empty, $datestring has to be a valid English date format
	 */
	static function date2Timestamp($datestring, $format = ""){
		if(!$format){
			return strtotime($datestring);
		}

		$replaceorder = array();

		$formatchars = array("Y", "y", "m", "n", "d", "j", "H", "G", "i", "s");

		$eregchars = "";

		foreach($formatchars as $char){
			$eregchars .= $char;
		}

		foreach($formatchars as $char){
			$format = str_replace("\\" . $char, "###we###" . ord($char) . "###we###", $format);
		}

		if(preg_match_all("/[$eregchars]/", $format, $matches, PREG_SET_ORDER)){
			foreach($matches as $match){
				if(is_array($match) && isset($match[0])){
					array_push($replaceorder, $match[0]);
				}
			}
		}

		$eregformat = preg_replace("/([$eregchars])/", "([0-9]+)", str_replace("/", "\\/", preg_quote($format)));

		foreach($formatchars as $char){
			$eregformat = str_replace("###we###" . ord($char) . "###we###", "\\" . $char, $eregformat);
		}

		$outarray = array(
			"hour" => 1,
			"minute" => 0,
			"second" => 0,
			"month" => 1,
			"day" => 1,
			"year" => 1970
		);

		if(preg_match_all('/' . $eregformat . '/', $datestring, $matches, PREG_SET_ORDER)){

			if(isset($matches[0]) && is_array($matches[0])){
				for($i = 1; $i < count($matches[0]); $i++){
					if(isset($replaceorder[$i - 1])){
						switch($replaceorder[$i - 1]){
							case "y":
							case "Y":
								$outarray["year"] = $matches[0][$i];
								break;
							case "m":
							case "n":
								$outarray["month"] = $matches[0][$i];
								break;
							case "d":
							case "j":
								$outarray["day"] = $matches[0][$i];
								break;
							case "H":
							case "G":
								$outarray["hour"] = $matches[0][$i];
								break;
							case "i":
								$outarray["minute"] = $matches[0][$i];
								break;
							case "s":
								$outarray["second"] = $matches[0][$i];
								break;
						}
					}
				}
			}

			return mktime(
					$outarray["hour"], $outarray["minute"], $outarray["second"], $outarray["month"], $outarray["day"], $outarray["year"]
			);
		}
		return 0;
	}

}
