<?php

/**
 * webEdition CMS
 *
 * $Rev: 5609 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 17:15:55 +0100 (Mon, 21 Jan 2013) $
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
//FIXME: unused??
class we_import_CSVFix extends CSV{

	var $FieldLengths;

	function __construct(){
		parent::__construct();
		$this->FieldLengths = array();
	}

	function addCSVField($name, $length, $type = ""){
		$cursor = count($this->FieldNames);

		if(!$name)
			$name = 'Feld ' . ($cursor + 1);

		$this->FieldNames[$cursor] = $name;
		$this->FieldLengths[$cursor] = $length;
		$this->setFieldType($cursor, $type);
	}

	function setFile($file){
		parent::setFile($file);
		$this->CSVData = explode("\n", trim($this->CSVData));
	}

	function parseCSV(){
		if($this->CSVData){
			if(!count($this->FieldLengths)){
				$this->CSVError[] = "CSV fields undefined.";
				return FALSE;
			}

			$currentLine = 0;

			foreach($this->CSVData as $line){
				$currentField = 0;
				$currentStringPos = 0;

				foreach($this->FieldLengths as $FieldLength){
					$value = trim(substr($line, $currentStringPos, $FieldLength));

					$this->Fields[$currentLine][$currentField] = $value;
					$currentStringPos += $FieldLength;
					$currentField++;
				}

				$currentLine++;
			}

			parent::convertFieldType();
			parent::applyFilter();
			$this->fetchCursor = 0;
		} else{
			$this->CSVError[] = "No data for import set.";
			return FALSE;
		}
	}

}
