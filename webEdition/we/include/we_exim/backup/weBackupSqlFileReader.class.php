<?php

/**
 * webEdition CMS
 *
 * $Rev: 4300 $
 * $Author: mokraemer $
 * $Date: 2012-03-18 16:36:04 +0100 (Sun, 18 Mar 2012) $
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
class weBackupSqlFileReader{

	function readLine($filename, &$data, &$offset, $lines = 1, $size = 0, $iscompressed = 0, &$create, &$insert){

		// set the number of lines
		$lines = 1;

		if($filename == '')
			return false;
		if(!is_readable($filename))
			return false;

		if($iscompressed == 0){
			$open = 'fopen';
			$seek = 'fseek';
			$tell = 'ftell';
			$gets = 'fgets';
			$close = 'fclose';
			$eof = 'feof';
		} else{
			$open = 'gzopen';
			$seek = 'gzseek';
			$tell = 'gztell';
			$gets = 'gzgets';
			$close = 'gzclose';
			$eof = 'gzeof';
		}


		$_fp = $open($filename, 'rb');

		if($_fp){

			if($seek($_fp, $offset, SEEK_SET) == 0){

				$i = 1;
				$_condition = false;

				do{
					$_buffer = '';
					$_count = 0;
					$_rsize = 8192; // read 8KB
					$_isend = false;
					do{

						$_buffer .= $gets($_fp, $_rsize);

						$_first = substr($_buffer, 0, 64);
						$_end = substr($_buffer, -20, 20);

						if(preg_match("/;\r?\n/", $_end)){

							if($this->preParse($_first, $create, $insert)){

								$_buffer = '';
								$_isend = $eof($_fp);
							} else{

								$_isend = true;
								$_buffer = preg_replace("/\r?\n/", ' ', $_buffer);
							}
						}

						// avoid endless loop
						$_count++;
						if($_count > 1000){
							break;
						}
					} while(!$_isend);

					//  check condition
					if($size > 0){
						if(empty($_buffer)){
							$_condition = false && !$eof($_fp);
						} else{
							$i = strlen($_buffer);
							if($i < $size){
								$_condition = true && !$eof($_fp);
								;
							} else{
								$_condition = false && !$eof($_fp);
								;
							}
						}
					} else if($lines > 0){
						if($i < $lines){
							$_condition = true && !$eof($_fp);
							;
						} else{
							$_condition = false && !$eof($_fp);
							;
						}
						$i++;
					}

					if(substr(trim($_buffer), 0, 1) != '#'){
						$data .= $_buffer;
					}
				} while($_condition);


				unset($_buffer);

				$offset = $tell($_fp);

				$close($_fp);

				if(empty($data)){
					return false;
				} else{
					return true;
				}
			} else{
				$close($_fp);
				return false;
			}
		}

		return false;
	}

	function preParse(&$content, &$create, &$insert){

		$create = $this->isCreateQuery($content);
		$insert = $this->isInsertQuery($content);

		$_table = $create . $insert;

		// if the table should't be imported
		if(weBackupUtil::getRealTableName($_table) === false){
			$create = '';
			$insert = '';
			return true;
		}

		return false;
	}

	/**
	 * Function: isCreateQuery
	 *
	 * Description: This function returns whether the given query is a "CREATE"
	 * query or not.
	 */
	function isCreateQuery(&$q){
		$m = array();

		if(preg_match("/CREATE[[:space:]]+TABLE[[:space:]]+([a-zA-Z0-9_-]+)/", $q, $m)){
			return $m[1];
		}

		return '';
	}

	/**
	 * Function: isInsertQuery
	 *
	 * Description: This function returns whether the given query is a "INSERT"
	 * query or not.
	 */
	function isInsertQuery(&$q){
		$m = array();

		if(preg_match("/INSERT[[:space:]]+INTO[[:space:]]+([a-zA-Z0-9_-]+)/", $q, $m)){
			return $m[1];
		}

		return '';
	}

}
