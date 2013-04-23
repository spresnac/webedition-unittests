<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_util
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * static class for various common filesystem operations
 * this is a merge of the old weFile class and the old we_live_tools.inc.php Script of webEdition 5.1.x and older
 *
 * @todo check if needed and if, then complete it and DON'T use old stuff like DB and other
 * */
abstract class we_util_File{

	public static function load($filename, $flags = "rb", $rsize = 8192){
		return weFile::load($filename, $flags, $rsize);
	}

	public static function loadLine($filename, $offset = 0, $rsize = 8192, $iscompressed = 0){
		return weFile::loadLine($filename, $offset, $rsize, $iscompressed);
	}

	public static function loadPart($filename, $offset = 0, $rsize = 8192, $iscompressed = 0){
		return weFile::loadPart($filename, $offset, $rsize, $iscompressed);
	}

	public static function save($filename, $content, $flags = "wb", $create_path = false){
		if($filename == "" || self::hasURL($filename)){
			return false;
		}
		if(file_exists($filename)){
			if(!is_writable($filename)){
				return false;
			}
		} elseif(($create_path && !self::mkpath(dirname($filename))) || (!is_writable(dirname($filename)))){
			return false;
		}

		$written = 0;

		$fp = @fopen($filename, $flags);
		if($fp){
			$written = fwrite($fp, $content);
			@fclose($fp);
			return $written;
		}
		return false;
	}

	public static function saveTemp($content, $filename = "", $flags = "wb"){
		return weFile::saveTemp($content, $filename, $flags);
	}

	public static function delete($filename){
		return weFile::delete($filename);
	}

	public static function hasURL($filename){
		return ((strtolower(substr($filename, 0, 4)) == "http") || (strtolower(substr($filename, 0, 4)) == "ftp"));
	}

	public static function getUniqueId($md5 = true){
		return weFile::getUniqueId($md5);
	}

	/**
	 * split a file into various parts of a predefined size
	 */
	public static function splitFile($filename, $path, $pattern = "", $split_size = 0, $marker = ""){

		if($pattern == ""){
			$pattern = basename($filename) . "%s";
		}
		$buff = "";
		$filename_tmp = "";
		$fh = fopen($filename, "rb");
		$num = -1;
		$open_new = true;
		$fsize = 0;

		$marker_size = strlen($marker);

		if($fh){
			while(!@feof($fh)) {
				@set_time_limit(60);
				$line = "";
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
					$fh_temp = fopen($filename_tmp, "wb");
					$open_new = false;
				}

				if($fh_temp){
					$buff .= $line;

					//print substr($buff,(0-($marker_size+1)))."<br>\n";


					$write = ($marker_size ? ((substr($buff, (0 - ($marker_size + 1))) == $marker . "\n") || (substr($buff, (0 - ($marker_size + 2))) == $marker . "\r\n")) : true);

					if($write){
						//print "WRITE<br>\n";
						$fsize += strlen($buff);
						fwrite($fh_temp, $buff);
						if(($split_size && $fsize > $split_size) || ($marker_size)){
							$open_new = true;
							@fclose($fh_temp);
							$fsize = 0;
						}
						$buff = "";
					}
				} else{
					return -1;
				}
			}
		} else{
			return -1;
		}
		if($fh_temp && $buff){
			fwrite($fh_temp, $buff);
		}
		@fclose($fh);

		return $num + 1;
	}

	public static function mkpath($path){
		$path = str_replace("\\", "/", $path);
		if(self::hasURL($path)){
			return false;
		}
		if($path != ""){
			return self::createLocalFolderByPath($path);
		}
		return false;
	}

	public static function hasGzip(){
		return weFile::hasGzip();
	}

	public static function hasZip(){
		return weFile::hasZip();
	}

	public static function hasBzip(){
		return weFile::hasBzip();
	}

	public static function hasCompression($comp){
		return weFile::hasCompression($comp);
	}

	public static function getComPrefix($compression){
		return weFile::getComPrefix($compression);
	}

	public static function getZExtension($compression){
		return weFile::getZExtension($compression);
	}

	public static function getCompression($filename){
		return weFile::getCompression($filename);
	}

	public static function compress($file, $compression = "gzip", $destination = "", $remove = true, $writemode = "wb"){

		if(!self::hasCompression($compression))
			return false;
		if($destination == "")
			$destination = $file;
		$prefix = weFile::getComPrefix($compression);
		$open = $prefix . "open";
		$write = $prefix . "write";
		$close = $prefix . "close";

		$fp = @fopen($file, "rb");
		if($fp){
			$zfile = $destination . ".gz";
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
		if($remove)
			@unlink($file);
		return $zfile;
	}

	public static function decompress($gzfile, $remove = true){
		$gzfp = @gzopen($gzfile, "rb");
		if($gzfp){
			$file = str_replace(".gz", "", $gzfile);
			if($file == $gzfile){
				$file = $gzfile . "xml";
			}
			if(($fp = @fopen($file, "wb"))){
				do{
					$data = gzread($gzfp, 8192);
					if(strlen($data) == 0){
						break;
					}
					fwrite($fp, $data);
				} while(true);
				fclose($fp);
			} else{
				gzclose($gzfp);
				return false;
			}
			gzclose($gzfp);
		} else{
			return false;
		}
		if($remove)
			@unlink($gzfile);
		return $file;
	}

	public static function isCompressed($file, $offset = 0){
		return weFile::isCompressed($file, $offset);
	}

	public static function saveFile($file_name, $sourceCode = ''){
		if(!self::createLocalFolderByPath(str_replace('\\', '/', dirname($file_name)))){
			return false;
		}
		$fh = @fopen($file_name, 'wb');
		if(!$fh){
			return false;
		}
		$ret = ($sourceCode ? fwrite($fh, $sourceCode) : true);
		fclose($fh);
		return $ret;
	}

	public static function createLocalFolder($RootDir, $path = ""){
		return self::createLocalFolderByPath($RootDir . $path);
	}

	public static function createLocalFolderByPath($completeDirPath){

		$returnValue = true;

		if(self::checkAndMakeFolder($completeDirPath, true)){
			return $returnValue;
		}

		$cf = array($completeDirPath);

		$parent = str_replace("\\", "/", dirname($completeDirPath));

		while(!self::checkAndMakeFolder($parent)) {
			$cf[] = $parent;
			$parent = str_replace("\\", "/", dirname($parent));
		}

		for($i = (count($cf) - 1); $i >= 0; $i--){
			$oldumask = @umask(0000);

			$mod = octdec(intval(WE_NEW_FOLDER_MOD));

			if(!@mkdir($cf[$i], $mod)){
				t_e('Warning', "Could not create local Folder at File.php/createLocalFolderByPath(): '" . $cf[$i] . "'");
				$returnValue = false;
			}
			@umask($oldumask);
		}

		return $returnValue;
	}

	public static function insertIntoCleanUp($path, $date){
		$DB_WE = new DB_WE();
		$DB_WE->query('INSERT INTO ' . CLEAN_UP_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'Path' => $DB_WE->escape($path),
				'Date' => intval($date)
			)) . ' ON DUPLICATE KEY UPDATE Date=' . intval($date));
	}

	public static function checkAndMakeFolder($path, $recursive = false){
		/* if the directory exists, we have nothing to do and then we return true  */
		if((file_exists($path) && is_dir($path)) || (strtolower(rtrim($_SERVER['DOCUMENT_ROOT'], '/')) == strtolower(rtrim($path, '/')))){
			return true;
		}

		// if instead of the directory a file exists, we delete the file and create the directory
		if(file_exists($path) && (!is_dir($path))){
			if(!we_util_File::deleteLocalFile($path)){
				t_e('Warning', "Could not delete File '" . $path . "'");
			}
		}

		$oldumask = @umask(0000);

		$mod = octdec(intval(WE_NEW_FOLDER_MOD));

		// check for directories: create it if we could no write into it:
		if(!@mkdir($path, $mod, $recursive)){
			@umask($oldumask);
			t_e('warning', "Could not create local Folder at 'we_util_File/checkAndMakeFolder()': '" . $path . "'");
			return false;
		}
		@umask($oldumask);
		return true;
	}

	/**
	 * checks permission to write in path $path and tries a chmod(0755)
	 */
	public static function checkWritePermissions($path, $mod = 0755, $nocreate = false){
		if(!is_file($path) && !is_dir($path)){
			t_e('warning',"we_util_File/checkWritePermissions() - target " . $path . " does not exist");
			return false;
		}
		if(is_writable($path)){
			return true;
		}
		$oldumask = @umask();
		@umask(0755);
		if(!@chmod($path, $mod)){
			return false;
		} else{
			return (is_writable($path));
		}
		@umask($oldumask);
	}

	public static function insertIntoErrorLog($text){
		t_e('warning', $text);
	}

	public static function getContentDirectFromDB($id, $name, $db = ""){
		/*
		  $db = we_io_DB::sharedAdapter();
		  $query = $db->query("SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID=? AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . LINK_TABLE . ".Name=?", array($id, $name));
		  $res = $query->fetchColumn(0);
		  if($res!='') {
		  return true;
		  }
		  return false;
		 */
	}

	/**
	 * @deprecated since - 05.06.2008
	 * please use moveFile() instead
	 */
	public static function renameFile($old, $new){
		return rename($old, $new);
	}

	/**
	 * copy a file
	 * due to windows limitations, the file has to be copied and the old file deleted afterwards.
	 * if $new exists already, windows will not rename the file $old
	 */
	public static function copyFile($old, $new){
		return (@copy($old, $new));
	}

	/**
	 * move/rename a file
	 * due to windows limitations, the file has to be copied and the old file deleted afterwards.
	 * if $new exists already, windows will not rename the file $old
	 */
	public static function moveFile($old, $new){
		if(!@rename($old, $new)){
			if(copy($old, $new)){
				unlink($old);
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * recursively moves a directory
	 * it will only move $dir if there is no directory in $target with the same name
	 */
	public static function moveDir($dir, $target){
		$dir = self::removeTrailingSlash($dir);
		$target = self::addTrailingSlash($target);
		$dirname = substr(strrchr($dir, "/"), 1);
		if(self::removeTrailingSlash($dir) == self::removeTrailingSlash($target)){
			t_e('notice',"source and destination are the same.");
			return true;
		}
		if(!@rename($dir, self::addTrailingSlash($target))){
			t_e('warning',"could not move directory " . $dir . " to " . self::addTrailingSlash($target) . ".");
			return false;
		} else{
			return true;
		}
	}

	public static function deleteLocalFolder($filename, $delAll = 0){
		if(!file_exists($filename))
			return false;
		if($delAll){
			$foo = (substr($filename, -1) == "/") ? $filename : ($filename . "/");
			$d = dir($filename);
			while(false !== ($entry = $d->read())) {
				if($entry != ".." && $entry != "."){
					$path = $foo . $entry;
					if(is_dir($path)){
						self::deleteLocalFolder($path, 1);
					} else{
						self::deleteLocalFile($path);
					}
				}
			}
			$d->close();
		}
		return @rmdir($filename);
	}

	public static function deleteLocalFile($filename){
		return (file_exists($filename) ? unlink($filename) : false);
	}

	/**
	 * recursively deletes a directory with all its contents
	 *
	 * @param string $path path to the directory that has to be deleted
	 * @param bool $nofiles does not delete any files but only empty subdirectories
	 */
	public static function rmdirr($path, $nofiles = false){
		//t_e("trying to recursively delete " . $path);
		if($nofiles && !is_dir($path)){
			t_e('warning',"ERROR: $path is no directory");
			return false;
		}
		if(!file_exists($path)){
			t_e('warning',"ERROR: could not find $path");
			return false;
		}
		// check if it is a file or a symbolic link;
		if(is_file($path) || is_link($path)){
			if($nofiles === false){
				if(@unlink($path)){
					return true;	
				} else {
					t_e('warning'," unable to delete file " . $path);
				}
			} else{
				//t_e(" -- skipping file " . $path);
			}
		}
		// loop through the folder
		$dir = dir($path);
		while(false !== $entry = $dir->read()) {
			if($entry == '.' || $entry == '..'){
				continue;
			}
			// Recurse
			//t_e(" -- trying to delete folder " . $path);
			self::rmdirr($path . DIRECTORY_SEPARATOR . $entry);
		}
		$dir->close();
		return @rmdir($path);
	}

	public static function addTrailingSlash($value){
		return self::removeTrailingSlash($value) . '/';
	}

	public static function removeTrailingSlash($value){
		return rtrim($value, '/');
	}

	public static function compressDirectoy($directoy, $destinationfile){
		if(!is_dir($directoy)){
			return false;
		}
		$DirFileObjectsArray = array();
		$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoy));
		foreach($DirFileObjects as $name => $object){
			if(substr($name, -2) != '/.' && substr($name, -3) != '/..'){
				$DirFileObjectsArray[] = $name;
			}
		}
		sort($DirFileObjectsArray);
		if(class_exists('Archive_Tar', true)){
			$tar_object = new Archive_Tar($destinationfile, true);
			$tar_object->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
			$tar_object->createModify($DirFileObjectsArray, '', $directoy);
		} else{
//FIXME: remove include
			include($GLOBALS['__WE_LIB_PATH__'] . DIRECTORY_SEPARATOR . 'additional' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'altArchive_Tar.class.php');
			$tar_object = new altArchive_Tar($gzfile, true);
			$tar_object->createModify($DirFileObjectsArray, '', $directoy);
		}
		return true;
	}

	public static function decompressDirectoy($gzfile, $destination){
		if(!is_file($gzfile)){
			return false;
		}
		if(class_exists('Archive_Tar', true)){
			$tar_object = new Archive_Tar($gzfile, true);
			$tar_object->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
			$tar_object->extractModify($destination, '');
		} else{
//FIXME: remove include
			include($GLOBALS['__WE_LIB_PATH__'] . DIRECTORY_SEPARATOR . 'additional' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'altArchive_Tar.class.php');
			$tar_object = new altArchive_Tar($gzfile, true);
			$tar_object->extractModify($destination, '');
		}
		return true;
	}

}