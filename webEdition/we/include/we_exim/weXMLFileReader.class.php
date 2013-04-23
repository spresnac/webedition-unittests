<?php

/**
 * webEdition CMS
 *
 * $Rev: 5743 $
 * $Author: mokraemer $
 * $Date: 2013-02-07 00:34:43 +0100 (Thu, 07 Feb 2013) $
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
abstract class weXMLFileReader{

	static $file = array();

	static function readLine($filename, &$offset, $lines = 1, $size = 0, $iscompressed = 0){
		$data='';
		$prefix = $iscompressed == 0 ? 'f' : weFile::getComPrefix('gzip');
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$tell = $prefix . 'tell';
		$gets = $prefix . 'gets';
		$eof = $prefix . 'eof';

		if(empty(self::$file)){
			if($filename == '' || !is_readable($filename)){
				return false;
			}
			if(!($fp = $open($filename, 'rb'))){
				return false;
			}
			self::$file = array(
				'fp' => $fp,
				'offset' => $offset,
			);
			$seek($fp, $offset);
		}

		if((self::$file['offset'] != $offset) && ($seek(self::$file['fp'], $offset, SEEK_SET) != 0)){
			self::closeFile();
			return false;
		}

		$i = 0;
		$condition = false;

		do{
			$buffer = '';
			$count = 0;
			$rsize = 8192; // read 8KB
			do{

				$buffer .= $gets(self::$file['fp'], $rsize);

				$first = substr($buffer, 0, 256);
				$end = substr($buffer, -20, 20);

				// chek if line is complete
				$iswestart = stripos($first, '<webEdition') !== false;
				$isweend = stripos($end, '</webEdition>') !== false;
				$isxml = preg_match('|<\?xml|i', $first);

				$isend = preg_match("|<!-- *webackup *-->|", $buffer) || empty($buffer);

				if($isend && self::preParse($first)){
					$buffer = '';
					$isend = $eof(self::$file['fp']);
				}

				if($iswestart || $isweend || $isxml){
					$buffer = '';
					$isend = $eof(self::$file['fp']);
				}
				// -----------------------------------------------------
				// avoid endless loop
				$count++;
				if($count > 100000){
					break;
				}
			} while(!$isend);

			//  check condition
			if($size > 0){
				if(empty($buffer)){
					$condition = false;
				} else{
					$i = strlen($buffer);
					$condition = ($i < $size ? !$eof(self::$file['fp']) : false );
				}
			} else if($lines > 0){
				$condition = ($i < $lines ? !$eof(self::$file['fp']) : false );
				$i++;
			}

			$data .= $buffer;
		} while($condition);

		unset($buffer);

		self::$file['offset'] = $offset = $tell(self::$file['fp']);

		return $data;
	}

	public static function closeFile(){
		if(empty(self::$file)){
			return;
		}
		gzclose(self::$file['fp']);
		self::$file = array();
	}

	static function preParse(&$content){
		return false;
	}

}
