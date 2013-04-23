<?php

/**
 * webEdition CMS
 *
 * $Rev: 5813 $
 * $Author: mokraemer $
 * $Date: 2013-02-14 09:54:36 +0100 (Thu, 14 Feb 2013) $
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
class we_customer_CSVImport extends we_import_CSV{

	var $hasHeader = 0;

	function setHeader($hasheader){
		$this->hasHeader = $hasheader;
	}

	function parseCSV(){
		if($this->CSVData){
			$akt_line = 0;
			$akt_field = 0;
			$akt_field_value = '';
			$last_char = '';
			$quote = 0;
			$field_input = 0;

			$head_complete = ($this->hasHeader ? 0 : 1);

			$end_cc = strlen($this->CSVData);

			for($cc = 0; $cc < $end_cc; $cc++){
				$akt_char = substr($this->CSVData, $cc, 1);

				if(($akt_char == $this->Enclosure) && ($last_char != '\\')){
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
						if(!$head_complete)
							$akt_line = 0;
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
					$this->Fields[$akt_line][$akt_field] = iconv($this->FromCharset, $this->ToCharset . '//TRANSLIT', trim($akt_field_value));
				} else{
					$this->FieldNames[$akt_field] = iconv($this->FromCharset, $this->ToCharset . '//TRANSLIT', trim($akt_field_value));
				}
			}

			if(!$akt_field){
				unset($this->Fields[$akt_line]);
			}

			$this->fetchCursor = 0;
		} else{
			$this->CSVError[] = 'CSV data empty.';
			return FALSE;
		}
	}

	function CSVFetchRow(){
		if($this->fetchCursor < $this->CSVNumRows()){
			$r = $this->Fields[$this->fetchCursor];
			$this->fetchCursor++;
			return $r;
		} else{
			$this->CSVError[] = 'No more data sets.';
			return FALSE;
		}
	}

}
