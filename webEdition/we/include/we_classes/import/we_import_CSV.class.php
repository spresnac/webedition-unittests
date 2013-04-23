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
class we_import_CSV extends CSV{

	var $FieldDelim;
	var $Enclosure;
	var $FromCharset;
	var $ToCharset;

	function __construct(){
		parent::__construct();
		$this->FieldDelim = ';';
		$this->FromCharset = DEFAULT_CHARSET;
		$this->ToCharset = DEFAULT_CHARSET;
	}

	function setDelim($delimiter){
		switch($delimiter){
			case '\t':
				$this->FieldDelim = "\t";
				break;
			case '':
				$this->FieldDelim = ' ';
				break;
			default:
				$this->FieldDelim = $delimiter;
		}
	}

	function setEnclosure($enclosure){
		$this->Enclosure = $enclosure;
	}

	function setFromCharset($charset){
		$this->FromCharset = $charset;
	}

	function setToCharset($charset){
		$this->ToCharset = $charset;
	}

	function parseCSV(){
		if(!$this->CSVData){
			$this->CSVError[] = "CSV data empty.";
			return FALSE;
		}

		$akt_line = 0;
		$akt_field = 0;
		$akt_field_value = '';
		$last_char = '';
		$quote = 0;
		$field_input = 0;
		$head_complete = 0;

		$end_cc = strlen($this->CSVData);

		for($cc = 0; $cc < $end_cc; $cc++){
			$akt_char = substr($this->CSVData, $cc, 1);

			if(($akt_char == $this->Enclosure) && ($last_char != "\\")){
				$quote = !$quote;
				$akt_char = '';
			}

			if(!$quote){
				if($akt_char == $this->FieldDelim){
					$field_input = !$field_input;
					$akt_char = '';
					$akt_field++;
					$akt_field_value = '';
				} else if(($akt_char == '\\') && $field_input){
					$field_input++;
					$quote++;
				} else if($akt_char == $this->Enclosure){
					$quote--;

					if($field_input){
						$field_input--;
					} else{
						$field_input++;
					}
				} else if($akt_char == "\n"){
					if($head_complete && (($akt_field + 1) > $this->CSVNumFields())){
						$this->CSVError[] = 'Fehler in <b>Zeile ' . ($akt_line + 2) . '</b>';
					}
					$akt_line++;
					$akt_field = 0;
					if(!$head_complete){
						$akt_line = 0;
					}
					$head_complete = 1;
					$akt_char = '';
					$akt_field_value = '';
				}
			}

			$last_char = $akt_char;
			if($akt_char == '\\'){
				$akt_char = '';
			}
			$akt_field_value .= $akt_char;

			if($head_complete){
				$this->Fields[$akt_line][$akt_field] = $this->FromCharset == $this->ToCharset ? trim($akt_field_value) : iconv($this->FromCharset, $this->ToCharset . '//TRANSLIT', trim($akt_field_value));
			} else{
				$this->FieldNames[$akt_field] = $this->FromCharset == $this->ToCharset ? trim($akt_field_value) : iconv($this->FromCharset, $this->ToCharset . '//TRANSLIT', trim($akt_field_value));
			}
		}

		if(!$akt_field){
			unset($this->Fields[$akt_line]);
		}

		$this->fetchCursor = 0;
	}

	function splitFile($path, $csv_fieldnames){
		if(!$this->isOK()){
			return false;
		}

		$num_files = 0;
		$fieldnames = ($csv_fieldnames) ? 0 : 1;
		$num_rows = $this->CSVNumRows();
		$num_fields = $this->CSVNumFields();

		we_util_File::createLocalFolder($path);

		for($i = 0; $i < $num_rows + $fieldnames; $i++){
			$d[0] = $d[1] = '';
			for($j = 0; $j < $num_fields; $j++){
				$d[1] .= (!$fieldnames) ? (($this->CSVFieldName($j) != '') ?
						$this->Enclosure . str_replace($this->Enclosure, '\\' . $this->Enclosure, $this->CSVFieldName($j)) . $this->Enclosure : '') : $this->Enclosure . 'f_' . $j . $this->Enclosure;
				$d[0] .= ($fieldnames && $i == 0) ?
					(($this->CSVFieldName($j) != "") ? $this->Enclosure . str_replace($this->Enclosure, '\\' . $this->Enclosure, $this->CSVFieldName($j)) . $this->Enclosure : '') :
					(($this->Fields[(!$fieldnames) ? $i : ($i - 1)][$j] != "") ?
						$this->Enclosure . str_replace($this->Enclosure, "\\" . $this->Enclosure, $this->Fields[(!$fieldnames) ? $i : ($i - 1)][$j]) . $this->Enclosure : "");
				if($j + 1 < $num_fields){
					$d[1] .= $this->FieldDelim;
					$d[0] .= $this->FieldDelim;
				}
			}
			weFile::save($path . '/temp_' . $i . '.csv', implode("\n", $d), 'wb');
			$num_files++;
		}
		return $num_files;
	}

}
