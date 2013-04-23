<?php

/**
 * webEdition CMS
 *
 * $Rev: 5848 $
 * $Author: mokraemer $
 * $Date: 2013-02-20 09:03:39 +0100 (Wed, 20 Feb 2013) $
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
abstract class weFile{

	static function load($filename, $flags = 'rb', $rsize = 8192, $iscompressed = 0){
		if($filename == ''){
			return false;
		}
		if(!self::hasURL($filename)){
			$filename = realpath($filename);
			/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
			  t_e('warning', 'Acess outside document_root forbidden!', $filename);
			  return;
			  } */

			if(!is_readable($filename)){
				return false;
			}
		}

		$prefix = $iscompressed == 0 ? 'f' : self::getComPrefix('gzip');
		$open = $prefix . 'open';
		$read = $prefix . 'read';
		$close = $prefix . 'close';

		$buffer = '';
		if(($fp = @$open($filename, $flags))){
			do{
				$data = $read($fp, $rsize);
				if(strlen($data) == 0){
					break;
				}
				$buffer .= $data;
			} while(true);
			$close($fp);
			return $buffer;
		}
		return false;
	}

	static function loadLine($filename, $offset = 0, $rsize = 8192, $iscompressed = 0){
		if($filename == '' || self::hasURL($filename) || !is_readable($filename)){
			return false;
		}
		$filename = realpath($filename);
		/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
		  t_e('warning', 'Acess outside document_root forbidden!', $filenam);
		  return;
		  } */

		$prefix = $iscompressed == 0 ? 'f' : self::getComPrefix('gzip');
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$read = $prefix . 'read';
		$close = $prefix . 'close';

		$buffer = '';
		if(($fp = $open($filename, 'rb'))){
			if($seek($fp, $offset, SEEK_SET) == 0){
				$buffer = $read($fp, $rsize);
				$close($fp);
				return $buffer;
			} else{
				$close($fp);
			}
		}
		return false;
	}

	static function loadPart($filename, $offset = 0, $rsize = 8192, $iscompressed = 0){
		if($filename == '' || self::hasURL($filename) || !is_readable($filename)){
			return false;
		}
		$filename = realpath($filename);
		/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
		  t_e('warning', 'Acess outside document_root forbidden!', $filename);
		  return;
		  } */

		$prefix = $iscompressed == 0 ? 'f' : self::getComPrefix('gzip');
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$read = $prefix . 'read';
		$close = $prefix . 'close';

		$buffer = '';
		if(($fp = @$open($filename, 'rb'))){
			if($seek($fp, $offset, SEEK_SET) == 0){
				$buffer = $read($fp, $rsize);
				$close($fp);
				return $buffer;
			} else{
				$close($fp);
			}
		}
		return false;
	}

	static function save($filename, $content, $flags = 'wb', $create_path = false){
		if($filename == '' || self::hasURL($filename) || (file_exists($filename) && !is_writable($filename))){
			t_e('error writing file', $filename);
			return false;
		} else{
			/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
			  t_e('warning', 'Acess outside document_root forbidden!', $filename, $oldFile,$_SERVER['DOCUMENT_ROOT']);
			  return;
			  } */
			if(($create_path && !self::mkpath(dirname($filename))) && (!is_writable(dirname($filename)))){
				t_e('failed to create file', $filename);
				return false;
			}
		}

		if(($fp = @fopen($filename, $flags))){
			$written = fwrite($fp, $content, strlen($content));
			@fclose($fp);
			return $written;
		}
		t_e('error writing file', $filename);
		return false;
	}

	static function saveTemp($content, $filename = '', $flags = 'wb'){
		if($filename == ''){
			$filename = self::getUniqueId();
		}
		$filename = TEMP_PATH . '/' . $filename;
		return (self::save($filename, $content, $flags) ? $filename : false);
	}

	static function delete($filename){
		if($filename == ''){
			return false;
		}
		if(!self::hasURL($filename)){
			if(is_writable($filename)){
				return (is_dir($filename) ? rmdir($filename) : unlink($filename));
			} else{
				return false;
			}
		}
		return false;
	}

	static function hasURL($filename){
		return ((strtolower(substr($filename, 0, 4)) == 'http') || (strtolower(substr($filename, 0, 3)) == 'ftp'));
	}

	static function getUniqueId($md5 = true){
		// md5 encrypted hash with the start value microtime(). The function
		// uniqid() prevents from simultanious access, within a microsecond.
		return ($md5 ? md5(uniqid(__FILE__, true)) : str_replace('.', '', uniqid('', true)));
		// #6590, changed from: uniqid(microtime()) and: FIXME: #6590: str_replace('.', '', uniqid('',true))'
	}

	/**
	 * Function: splitFile
	 *
	 * Description: This function splits a file.
	 */
	static function splitFile($filename, $path, $pattern = '', $split_size = 0, $marker = ''){

		if($pattern == ''){
			$pattern = basename($filename) . '%s';
		}
		$buff = '';
		$filename_tmp = '';
		$fh = fopen($filename, 'rb');
		$num = -1;
		$open_new = true;
		$fsize = 0;

		$marker_size = strlen($marker);

		if($fh){
			while(!@feof($fh)) {
				@set_time_limit(60);
				$line = '';
				$findline = false;

				while($findline == false && !@feof($fh)) {
					$line .= @fgets($fh, 4096);
					if(substr($line, -1) == "\n"){
						$findline = true;
					}
				}

				if($open_new){
					$num++;
					$filename_tmp = sprintf($path . $pattern, $num);
					$fh_temp = fopen($filename_tmp, 'wb');
					$open_new = false;
				}

				if($fh_temp){
					$buff.=$line;
					$write = false;

					//print substr($buff,(0-($marker_size+1)))."<br>\n";

					if($marker_size){
						$write = ((substr($buff, (0 - ($marker_size + 1))) == $marker . "\n") || (substr($buff, (0 - ($marker_size + 2))) == $marker . "\r\n"));
					} else{
						$write = true;
					}

					if($write){
						//print "WRITE<br>\n";
						$fsize+=strlen($buff);
						fwrite($fh_temp, $buff);
						if(($split_size && $fsize > $split_size) || ($marker_size)){
							$open_new = true;
							@fclose($fh_temp);
							$fsize = 0;
						}
						$buff = '';
					}
				} else{
					return -1;
				}
			}
		} else{
			return -1;
		}
		if($fh_temp){
			if($buff){
				fwrite($fh_temp, $buff);
			}
			@fclose($fh_temp);
		}
		@fclose($fh);

		return $num + 1;
	}

	static function mkpath($path){
		$path = str_replace('\\', '/', $path);
		if(self::hasURL($path))
			return false;
		if($path != ''){
			return we_util_File::createLocalFolderByPath($path);
		}
		return false;
	}

	static function hasGzip(){
		return function_exists('gzopen');
	}

	static function hasZip(){
		return function_exists('zip_open');
	}

	static function hasBzip(){
		return function_exists('bzopen');
	}

	static function hasCompression($comp){
		switch($comp){
			case 'gzip':
				return self::hasGzip();
			case 'zip':
				return self::hasZip();
			case 'bzip':
				return self::hasBzip();
			default:
				return false;
		}
	}

	static function getComPrefix($compression){
		switch($compression){
			case 'gzip':
				return 'gz';
			case 'zip':
				return 'zip_';
			case 'bzip':
				return 'bz';
			default:
				return 'f';
		}
	}

	static function getZExtension($compression){
		switch($compression){
			case 'gzip':
				return 'gz';
			case 'zip':
				return 'zip';
			case 'bzip':
				return 'bz';
			default:
				return '';
		}
	}

	static function getCompression($filename){
		$compressions = array('gzip', 'zip', 'bzip');
		foreach($compressions as $val){
			if(stripos(basename($filename), '.' . weFile::getZExtension($val)) !== false){
				return $val;
			}
		}
		return 'none';
	}

	static function compress($file, $compression = 'gzip', $destination = '', $remove = true, $writemode = 'wb'){
		if(!self::hasCompression($compression)){
			t_e('compression not available',$compression);
			return false;
		}
		if($destination == ''){
			$destination = $file;
		}
		$zfile = $destination . '.' . self::getZExtension($compression);

		if(self::isCompressed($file)){
			if($remove){
				rename($file, $zfile);
			} else{
				copy($file, $zfile);
			}
			return $zfile;
		}
		$prefix = self::getComPrefix($compression);
		$open = $prefix . 'open';
		$write = $prefix . 'write';
		$close = $prefix . 'close';

		$fp = @fopen($file, 'rb');
		if($fp){
			$gzfp = $open($zfile, $writemode);
			if($gzfp){
				do{
					$data = fread($fp, 8192);
					$_data_size = strlen($data);
					if($_data_size == 0){
						break;
					}
					$_written = $write($gzfp, $data, $_data_size);
					if($_data_size != $_written){
						return false;
					}
				} while(true);
				$close($gzfp);
			} else{
				fclose($fp);
				return false;
			}
			fclose($fp);
		} else{
			return false;
		}
		if($remove){
			self::delete($file);
		}
		return $zfile;
	}

	static function decompress($gzfile, $remove = true){
		$gzfp = @gzopen($gzfile, 'rb');
		if($gzfp){
			$file = str_replace('.gz', '', $gzfile);
			if($file == $gzfile)
				$file = $gzfile . 'xml';
			$fp = @fopen($file, 'wb');
			if($fp){
				do{
					$data = gzread($gzfp, 8192);
					if(strlen($data) == 0)
						break;
					fwrite($fp, $data);
				} while(true);
				fclose($fp);
			}
			else{
				gzclose($gzfp);
				return false;
			}
			gzclose($gzfp);
		} else{
			return false;
		}
		if($remove){
			self::delete($gzfile);
		}
		return $file;
	}

	static function isCompressed($file, $offset = 0){
		if(($fh = @fopen($file, 'rb'))){
			if(fseek($fh, $offset, SEEK_SET) == 0){
				// according to rfc1952 the first two bytes identify the format
				$_id1 = fgets($fh, 2);
				$_id2 = fgets($fh, 2);
				if((ord($_id1) == 31) && (ord($_id2) == 139)){
					return true;
				}
			}
			fclose($fh);
		}
		return false;
	}

}
