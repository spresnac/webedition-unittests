<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
class CSV{

	var $CSVFile;
	var $CSVData;
	var $CSVError;
	var $FieldNames;
	var $Fields;
	var $FieldTypes;
	var $fetchCursor;
	var $Filter;

	function __construct(){
		$this->CSVError = array();
		$this->CSVData = "";
		$this->Filter = array();
	}

	function setFile($file){
		$this->CSVFile = $file;
		if(file_exists($this->CSVFile) && ($this->CSVFile != "none") && !empty($this->CSVFile)){
			$this->setData(implode("", file($this->CSVFile)));
		} else{
			$this->CSVError[] = "The file " . $file . " does not exist or is empty.";
			return FALSE;
		}
	}

	function setData($string){
		$this->CSVData = $string;
		$this->Fields = array();
		$this->FieldTypes = array();
		$this->FieldNames = array();
	}

	function CSVFetchRow(){
		if($this->fetchCursor <= $this->CSVNumRows()){
			$r = $this->Fields[$this->fetchCursor];
			$this->fetchCursor++;
			return $r;
		} else{
			$this->CSVError[] = "No more data sets.";
			return FALSE;
		}
	}

	function CSVFetchArray($resultTyp = "BOTH"){
		if($this->fetchCursor <= ($this->CSVNumRows()) - 1){

			if(($resultTyp == "NUM") || ($resultTyp == "BOTH")){
				$r = $this->CSVFetchRow();
				if($resultTyp == "NUM")
					return $r;

				$this->fetchCursor--;
			}
			if(($resultTyp == "ASSOC") || ($resultTyp == "BOTH")){

				if(is_array($this->Fields[$this->fetchCursor])){
					reset($this->Fields[$this->fetchCursor]);
					while(list($field_id, $field) = each($this->Fields[$this->fetchCursor])) {
						$r[$this->FieldNames[$field_id]] = $field;
					}
				}
			}
			$this->fetchCursor++;
			return $r;
		} else{
			$this->CSVError[] = "No more data sets.";
			return FALSE;
		}
	}

	function CSVFetchFieldNames(){
		return $this->FieldNames;
	}

	function CSVFieldName($field_id){
		return $this->FieldNames[$field_id];
	}

	function CSVNumRows(){
		return count($this->Fields);
	}

	function CSVNumFields(){
		return count($this->FieldNames);
	}

	function setCursor($pos){
		$this->fetchCursor = $pos;
	}

	function getCursor(){
		return $this->fetchCursor;
	}

	function resetCursor(){
		$this->setCursor(0);
	}

	function getFieldID($search_field){
		if(!is_array($this->FieldNames))
			return FALSE;
		foreach($this->FieldNames as $field_id => $field_name){

			if(trim($search_field) == trim($field_name)){
				return $field_id;
			}
		}
		return FALSE;
	}

	function echoCSVError(){
		foreach($this->CSVError as $pos => $error_str){
			echo "- " . ($pos + 1) . ". " . $error_str . "<br>";
		}
	}

	function isOK($error_output = TRUE){
		if($error_output)
			$this->echoCSVError();
		return ((!empty($this->CSVError)) ? FALSE : TRUE);
	}

	function array_merge_better($a1, $a2){
		if(!is_array($a1)){
			$a1 = array();
		}
		if(!is_array($a2)){
			$a2 = array();
		}


		$newarray = $a1;

		while(list($key, $val) = each($a2)) {
			if(is_array($val) && is_array($newarray[$key])){
				$newarray[$key] = $this->array_merge_better($newarray[$key], $val);
			} else{
				$newarray[$key] = $val;
			}
		}
		return $newarray;
	}

	function generateIniValue($array, $filename){
		$val = '';
		foreach($array as $key => $val){
			$val.="$key = $val;\n";
		}
		weFile::save($filename, $val, 'w');
	}

}