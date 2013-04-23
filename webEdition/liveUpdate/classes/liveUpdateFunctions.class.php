<?php

/**
 * webEdition CMS
 *
 * $Rev: 5965 $
 * $Author: mokraemer $
 * $Date: 2013-03-16 17:28:12 +0100 (Sat, 16 Mar 2013) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * This class contains all functions needed for the update process
 * TBD if we divide this class in several classes
 */
class liveUpdateFunctions{

	private $QueryLog = array(
		'success' => array(),
		'tableChanged' => array(),
		'error' => array(),
		'entryExists' => array(),
	);

	/*
	 * Functions for updatelog
	 */

	function insertUpdateLogEntry($action, $version, $errorCode){
		$GLOBALS['DB_WE']->query('INSERT INTO ' . UPDATE_LOG_TABLE . we_database_base::arraySetter(array(
				'aktion' => $action,
				'versionsnummer' => $version,
				'error' => $errorCode
		)));
	}

	/**
	 * @param string $type
	 * @param string $premessage
	 * @param integer $errorCode
	 * @param string $version
	 */
	function insertQueryLogEntries($type, $premessage = '', $errorCode, $version){
		// insert notices first
		if(isset($this->QueryLog[$type])){
			$message = $premessage;

			foreach($this->QueryLog[$type] as $noticeMessage){
				$message .= "<br />$noticeMessage\n";
			}
			$this->insertUpdateLogEntry($message, $version, $errorCode);
		}
	}

	/**
	 * Decode encoded strings submit from liveupdater
	 *
	 * @param string $string
	 * @return string
	 */
	function decodeCode($string){
		return base64_decode($string);
	}

	/**
	 * prepares given php-code
	 * - replaces doc_root
	 * - edits extension of all containing files
	 *
	 * @return string
	 */
	function preparePhpCode($content, $needle, $replace){
		$content = $this->replaceExtensionInContent($content, $needle, $replace);
		return $this->checkReplaceDocRoot($content);
	}

	/**
	 * replaces extension of content
	 *
	 * @param unknown_type $content
	 * @param unknown_type $replace
	 * @param unknown_type $needle
	 * @return unknown
	 */
	function replaceExtensionInContent($content, $needle, $replace){
		$content = str_replace($needle, $replace, $content);
		return $content;
	}

	function replaceDocRootNeeded(){
		return (!(isset($_SERVER['DOCUMENT' . '_ROOT']) && $_SERVER['DOCUMENT' . '_ROOT'] == LIVEUPDATE_SOFTWARE_DIR));
	}

	/**
	 * checks if document root exists, and replaces $_SERVER['DOCMENT_ROOT'] in
	 * $content if needed
	 *
	 * @param string $content
	 * @return string
	 */
	function checkReplaceDocRoot($content){
		if(self::replaceDocRootNeeded){
			$content = str_replace(array(
				'$_SERVER[\'DOCUMENT_ROOT\']',
				"\$_SERVER[\"DOCUMENT_ROOT\"]",
				'$GLOBALS[\'DOCUMENT_ROOT\']',
				"\$GLOBALS[\"DOCUMENT_ROOT\]",
				), '"' . LIVEUPDATE_SOFTWARE_DIR . '"', $content);
		}
		return $content;
	}

	/**
	 * fills given array with all files in given dir
	 *
	 * @param array $allFiles
	 */
	function getFilesOfDir(&$allFiles, $baseDir){
		if(file_exists($baseDir)){
			$dh = opendir($baseDir);
			while(($entry = readdir($dh))) {
				if($entry != "" && $entry != "." && $entry != ".."){
					$_entry = $baseDir . "/" . $entry;
					if(!is_dir($_entry)){
						$allFiles[] = $_entry;
					}

					if(is_dir($_entry)){
						$this->getFilesOfDir($allFiles, $_entry);
					}
				}
			}
			closedir($dh);
		}
	}

	/**
	 * deletes $dir and all files/dirs inside
	 *
	 * @param string $dir
	 */
	function deleteDir($dir){
		if(strpos($dir, './') !== false){
			return true;
		}

		if(!file_exists($dir)){
			return true;
		}

		$dh = opendir($dir);
		if($dh){
			while(($entry = readdir($dh))) {
				if($entry != '' && $entry != "." && $entry != '..'){
					$_entry = $dir . '/' . $entry;
					if(is_dir($_entry)){
						$this->deleteDir($_entry);
					} else{
						$this->deleteFile($_entry);
					}
				}
			}
			closedir($dh);
			return rmdir($dir);
		} else{
			return true;
		}
	}

	/**
	 * Reads filecontent in a string and returns it
	 *
	 * @param string $filePath
	 * @return string
	 */
	function getFileContent($filePath){
		$content = '';
		$fh = fopen($filePath, 'rb');
		if($fh){
			$content = fread($fh, filesize($filePath));
			fclose($fh);
		}

		return $content;
	}

	/**
	 * writes filecontent in a file
	 *
	 * @param string $filePath
	 * @param string $newContent
	 * @return boolean
	 */
	function filePutContent($filePath, $newContent){
		if($this->checkMakeDir(dirname($filePath))){
			$fh = fopen($filePath, 'wb');
			if($fh){
				fwrite($fh, $newContent, strlen($newContent));
				fclose($fh);
				if(!chmod($filePath, 0755)){
					return false;
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * This function checks if given dir exists, if not tries to create it
	 *
	 * @param string $dirPath
	 * @return boolean
	 */
	function checkMakeDir($dirPath, $mod = 0755){
		// open_base_dir - seperate document-root from rest
		$dirPath = rtrim(str_replace(array('///', '//'), '/', $dirPath), '/');

		$dir = (defined('LIVEUPDATE_SOFTWARE_DIR') ? LIVEUPDATE_SOFTWARE_DIR : WEBEDITION_PATH);
		if(strpos($dirPath, $dir) === 0){
			$preDir = $dir;
			$dir = substr($dirPath, strlen($dir));
		} else{
			$preDir = '';
			$dir = $dirPath;
		}

		$pathArray = explode('/', $dir);
		$path = $preDir;

		foreach($pathArray as $subPath){
			$path .= $subPath;
			if($subPath != "" && !is_dir($path)){
				if(!(file_exists($path) || mkdir($path, $mod))){
					return false;
				}
			}
			$path .= '/';
		}

		if(!is_writable($dirPath)){
			if(!chmod($dirPath, $mod)){
				return false;
			}
		}
		return true;
	}

	/**
	 * @param string $file
	 * @return boolean true if the file is not existent after this call
	 */
	function deleteFile($file){
		if(file_exists($file)){
			return @unlink($file);
		} else{
			return true;
		}
	}

	/**
	 * moves $source file to new $destination
	 *
	 * @param string $source
	 * @param string $destination
	 * @return boolean false if move was not successful
	 */
	function moveFile($source, $destination){

		if($source == $destination){
			return true;
		}

		if($this->checkMakeDir(dirname($destination))){
			if($this->deleteFile($destination)){
				if(!isset($_SESSION['weS']['moveOk'])){
					touch($source . 'x');
					$_SESSION['weS']['moveOk'] = rename($source . 'x', $destination . 'x');
					$this->deleteFile($destination . 'x');
				}

				if($_SESSION['weS']['moveOk']){
					return rename($source, $destination);
				}
				//rename seems to have problems - we do it old school way: copy, on success delete
				if(copy($source, $destination)){
					$this->deleteFile($source);
					//should we handle file deletion?
					return true;
				} else{
					return false;
				}
			} else{
				return false;
			}
		} else{
			return false;
		}
	}

	/**
	 * returns if selected file is a php file or not, important also check html files
	 *
	 * @param string $path
	 * @return boolean
	 */
	function isPhpFile($path){

		$pattern = "/\.([^\..]+)$/";

		if(preg_match($pattern, $path, $matches)){
			$ext = strtolower($matches[1]);
			if(($ext == 'jpg' || $ext == 'gif' || $ext == 'jpeg' || $ext == 'sql')){
				return false;
			}
		}
		return true;
	}

	/**
	 * This file searchs $needle in given file and replaces it with $replace
	 * If needle is empty the whole file is overwritten. Also
	 * $_SERVER[DOCUMENT_ROOT] is replaced if necessary
	 *
	 * @param string $filePath
	 * @param string $replace
	 * @param string $needle
	 * @return boolean
	 */
	function replaceCode($filePath, $replace, $needle = ''){
		if(!$this->replaceDocRootNeeded()){
			return true;
		}

		// decode parameters
		$needle = $this->decodeCode($needle);
		$replace = $this->decodeCode($replace);

		if(file_exists($filePath)){
			$oldContent = $this->getFileContent($filePath);
			$replace = $this->checkReplaceDocRoot($replace);
			$newContent = ($needle ? preg_replace('/' . preg_quote($needle) . '/', $replace, $oldContent) : $replace );

			return ($this->filePutContent($filePath, $newContent));
		} else{
			return false;
		}
		return true;
	}

	/*
	 * Functions for patches
	 */

	/**
	 * executes patch.
	 *
	 * @param string $path
	 * @return boolean
	 */
	function executePatch($path){
		include_once($path);
		return true;

		if(file_exists($path)){

			$code = $this->getFileContent($path);
			/** läuft nicht durch
			  $patchSuccess = eval('?>' . escapeshellcmd($code));
			 */
			$patchSuccess = eval('?>' . $code);
			if($patchSuccess === false){
				return false;
			} else{
				return true;
			}
		}
		return true;
	}

	/*
	 * Functions for manipulating database
	 */

	/**
	 * returns array with all columns of given tablename
	 *
	 * @param string $tableName
	 * @return array
	 */
	function getFieldsOfTable($tableName, $db){

		$fieldsOfTable = array();
		$db->query('DESCRIBE ' . $db->escape($tableName));

		while($db->next_record()) {
			$fieldsOfTable[$db->f('Field')] = array(
				'Field' => $db->f('Field'),
				'Type' => $db->f('Type'),
				'Null' => $db->f('Null'),
				'Key' => $db->f('Key'),
				'Default' => $db->f('Default'),
				'Extra' => $db->f('Extra')
			);
		}
		return $fieldsOfTable;
	}

	/**
	 * returns array with key information of a table by tablename
	 *
	 * @param string $tableName
	 * @return array
	 */
	function getKeysFromTable($tableName){
		$db = new DB_WE();
		$keysOfTable = array();
		$db->query('SHOW INDEX FROM ' . $db->escape($tableName));
		while($db->next_record()) {
			if($db->f('Key_name') == 'PRIMARY'){
				$indexType = 'PRIMARY';
			} else if($db->f('Comment') == 'FULLTEXT' || $db->f('Index_type') == 'FULLTEXT'){// this also depends on mysqlVersion
				$indexType = 'FULLTEXT';
			} else if($db->f('Non_unique') == '0'){
				$indexType = 'UNIQUE';
			} else{
				$indexType = 'INDEX';
			}

			if(!isset($keysOfTable[$db->f('Key_name')]) || !in_array($indexType, $keysOfTable[$db->f('Key_name')])){
				$keysOfTable[$db->f('Key_name')]['index'] = $indexType;
			}
			$keysOfTable[$db->f('Key_name')][$db->f('Seq_in_index')] = $db->f('Column_name') . ($db->f('Sub_part') ? '(' . $db->f('Sub_part') . ')' : '');
		}

		return $keysOfTable;
	}

	/**
	 * expects array from getFieldsOfTable and returns generated queries to
	 * alter these fields
	 *
	 * @param array $fields
	 * @param string $tableName
	 * @param boolean $newField
	 * @return unknown
	 */
	function getAlterTableForFields($fields, $tableName, $isNew = false){
		$queries = array();

		foreach($fields as $fieldName => $fieldInfo){

			$default = '';

			$null = (strtoupper($fieldInfo['Null']) == "YES" ? ' NULL' : ' NOT NULL');

			if(($fieldInfo['Default']) != ""){
				$default = 'DEFAULT ' . (($fieldInfo['Default']) == 'CURRENT_TIMESTAMP' ? 'CURRENT_TIMESTAMP' : '\'' . $fieldInfo['Default'] . '\'');
			} else{
				if(strtoupper($fieldInfo['Null']) == "YES"){
					$default = ' DEFAULT NULL';
				}
			}
			$extra = strtoupper($fieldInfo['Extra']);
			//note: auto_increment cols must have an index!
			if(strpos($extra, 'AUTO_INCREMENT') !== false){
				$keyfound = false;
				$Currentkeys = $this->getKeysFromTable($tableName);
				foreach($Currentkeys as $ckeys){
					foreach($ckeys as $k){
						if(stripos($k, $fieldName) !== false){
							$keyfound = true;
						}
					}
				}
				if(!$keyfound){
					$extra .= ' FIRST, ADD INDEX _temp (' . $fieldInfo['Field'] . ')';
				}
			}

			if($isNew){
				//Bug #4431, siehe unten
				$queries[] = "ALTER TABLE `$tableName` ADD `" . $fieldInfo['Field'] . '` ' . $fieldInfo['Type'] . " $null $default $extra";
			} else{
				//Bug #4431
				// das  mysql_real_escape_string bei $fieldInfo['Type'] f�hrt f�r enum dazu, das die ' escaped werden und ein Syntaxfehler entsteht (nicht abgeschlossene Zeichenkette
				$queries[] = "ALTER TABLE `$tableName` CHANGE `" . $fieldInfo['Field'] . '` `' . $fieldInfo['Field'] . '` ' . $fieldInfo['Type'] . " $null $default $extra";
			}
		}
		return $queries;
	}

	/**
	 * returns array with queries to update keys of table
	 *
	 * @param array $fields
	 * @param string $tableName
	 * @param boolean $isNew
	 * @return array
	 */
	function getAlterTableForKeys($fields, $tableName, $isNew){
		$queries = array();

		foreach($fields as $key => $indexes){
			//escape all index fields
			$indexes = array_map('addslashes', $indexes);

			$type = $indexes['index'];
			$mysl = '`';
			if($type == 'PRIMARY'){
				$key = 'KEY';
				$mysl = '';
			}
			//index is not needed any more and disturbs implode
			unset($indexes['index']);
			$myindexes = array();
			foreach($indexes as $index){
				if(strpos($index, '(') === false){
					$myindexes[] = '`' . $index . '`';
				} else{
					$myindexes[] = '`' . str_replace('(', '`(', $index);
				}
			}
			$queries[] = 'ALTER TABLE ' . $tableName . ' ' . ($isNew ? '' : ' DROP ' . ($type == 'PRIMARY' ? $type : 'INDEX') . ' ' . $mysl . $key . $mysl . ' , ') . ' ADD ' . $type . ' ' . $mysl . $key . $mysl . ' (' . implode(',', $myindexes) . ')';
		}
		return $queries;
	}

	/**
	 *
	 * @param string $path
	 * @return boolean
	 */
	function isInsertQueriesFile($path){
		return preg_match("/^(.){3}_insert_(.*).sql/", basename($path));
	}

	/**
	 * executes all queries in a single file
	 * - there is one query, if create-statement
	 * - many queris, if insert statements
	 *
	 *
	 * @param string $path
	 * @return boolean
	 */
	function executeQueriesInFiles($path){
		$db = new DB_WE();
		if($this->isInsertQueriesFile($path)){
			$success = true;
			$queryArray = file($path);
			if($queryArray){
				foreach($queryArray as $query){
					if(trim($query)){
						$success &= $this->executeUpdateQuery($query, $db);
					}
				}
			}
		} else{
			$content = $this->getFileContent($path);
			$queries = explode("/* query separator */", $content);
			$success = true;
			foreach($queries as $query){
				$success &= $this->executeUpdateQuery($query, $db);
			}
		}
		return $success;
	}

	/**
	 * updates the database with given dump.
	 *
	 * @param string $query
	 */
	function executeUpdateQuery($query, $db = ''){
		$db = ($db ? $db : new DB_WE());

		// when executing a create statement, try to create table,
		// change fields when needed.


		if(strpos($query, '###INSTALLONLY###') !== false){// potenzielles Sicherheitsproblem, nur im LiveUpdate nicht ausf�hren
			return true;
		}

		$query = str_replace(array('###TBLPREFIX###', '###UPDATEONLY###'), array(LIVEUPDATE_TABLE_PREFIX, ''), trim($query));
		$matches = array();
		if(preg_match('/###UPDATEDROPCOL\((.*),(.*)\)###/', $query, $matches)){
			$db->query('SHOW COLUMNS FROM ' . $db->escape($matches[2]) . ' WHERE Field="' . $matches[1] . '"');
			$query = ($db->num_rows() ? 'ALTER TABLE ' . $db->escape($matches[2]) . ' DROP COLUMN ' . $db->escape($matches[1]) : '');
		}
		/* if (LIVEUPDATE_TABLE_PREFIX && strpos($query,'###TBLPREFIX###')===false) {

		  $query = preg_replace("/^INSERT INTO /", "INSERT INTO " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^INSERT IGNORE INTO /", "INSERT IGNORE INTO " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^CREATE TABLE /", "CREATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^DELETE FROM /", "DELETE FROM " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^ALTER TABLE /", "ALTER TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^RENAME TABLE /", "RENAME TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^TRUNCATE TABLE /", "TRUNCATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		  $query = preg_replace("/^DROP TABLE /", "DROP TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);

		  $query = @str_replace(LIVEUPDATE_TABLE_PREFIX.'`', '`'.LIVEUPDATE_TABLE_PREFIX, $query);
		  } */

		// second, we need to check if there is a collation
		$Charset = we_database_base::getCharset();
		$Collation = we_database_base::getCollation();
		if($Charset != '' && $Collation != ''){
			if(stripos($query, "CREATE TABLE ") === 0){
				if(strtoupper($Charset) == 'UTF-8'){//#4661
					$Charset = 'utf8';
				}
				if(strtoupper($Collation) == 'UTF-8'){//#4661
					$Collation = 'utf8_general_ci';
				}
				$query = preg_replace('/;$/', ' CHARACTER SET ' . $Charset . ' COLLATE ' . $Collation . ';', $query, 1);
			}
		}

		if($query == '' || $db->query($query)){
			return true;
		} else{

			switch($db->Errno){

				case '1050': // this table already exists
					// the table already exists,
					// make tmptable and check these tables
					$namePattern = '/CREATE TABLE (\w+) \(/';
					preg_match($namePattern, $query, $matches);

					if($matches[1]){

						// get name of table and build name of temptable
						// realname of the new table
						$tableName = $matches[1];

						// tmpname - this table is to compare the incoming dump
						// with existing table
						$tmpName = '__we_delete_update_temp_table__';
						$backupName = trim($tableName, '`') . '_backup';

						$db->query('DROP TABLE IF EXISTS ' . $db->escape($tmpName)); // delete table if already exists
						$db->query('DROP TABLE IF EXISTS ' . $db->escape($backupName)); // delete table if already exists
						$db->query('SHOW CREATE TABLE ' . $db->escape($tableName));
						list(, $orgTable) = ($db->next_record() ? $db->Record : array('', ''));
						$orgTable = preg_replace($namePattern, 'CREATE TABLE ' . $db->escape($backupName) . ' (', $orgTable);

						// create temptable
						$tmpQuery = preg_replace($namePattern, 'CREATE TABLE ' . $db->escape($tmpName) . ' (', $query);
						$db->query(trim($tmpQuery));

						// get information from existing and new table
						$origTable = $this->getFieldsOfTable($tableName, $db);
						$newTable = $this->getFieldsOfTable($tmpName, $db);

						// get keys from existing and new table
						$origTableKeys = $this->getKeysFromTable($tableName);
						$newTableKeys = $this->getKeysFromTable($tmpName);


						// determine changed and new fields.
						$changeFields = array(); // array with changed fields
						$addFields = array(); // array with new fields

						foreach($newTable as $fieldName => $newField){
							if(isset($origTable[$fieldName])){ // field exists
								if(!($newField['Type'] == $origTable[$fieldName]['Type'] && $newField['Null'] == $origTable[$fieldName]['Null'] && $newField['Default'] == $origTable[$fieldName]['Default'] && $newField['Extra'] == $origTable[$fieldName]['Extra'])){
									$changeFields[$fieldName] = $newField;
								}
							} else{ // field does not exist
								$addFields[$fieldName] = $newField;
							}
						}

						// determine new keys
						// moved down after change and addfields
						// get all queries to add/change fields, keys
						$alterQueries = array();

						// get all queries to change existing fields
						if(!empty($changeFields)){
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($changeFields, $tableName));
						}
						if(!empty($addFields)){
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($addFields, $tableName, true));
						}

						//new position to determine new keys
						$addKeys = array();
						$changedKeys = array();
						foreach($newTableKeys as $keyName => $indexes){

							if(isset($origTableKeys[$keyName])){
								//index-type changed
								if($origTableKeys[$keyName]['index'] != $indexes['index']){
									$changedKeys[$keyName] = $indexes;
									continue;
								}

								for($i = 1; $i < count($indexes); $i++){
									if(!in_array($indexes[$i], $origTableKeys[$keyName])){
										$changedKeys[$keyName] = $indexes;
										break;
									}
								}
							} else{
								$addKeys[$keyName] = $indexes;
							}
						}

						// get all queries to change existing keys
						if(!empty($addKeys)){
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($addKeys, $tableName, true));
						}

						if(!empty($changedKeys)){
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($changedKeys, $tableName, false));
						}

						//clean-up, if there is still a temporary index - make sure this is the first statement, since new temp might be created
						if(isset($origTableKeys['_temp'])){
							$alterQueries = array_merge(array('ALTER TABLE `' . $tableName . '` DROP INDEX _temp'), $alterQueries);
						}

						if(!empty($alterQueries)){
							// execute all queries
							$success = true;
							$duplicate = false;
							foreach($alterQueries as $_query){
								if(!trim($_query)){
									continue;
								}
								if($db->query(trim($_query))){
									$this->QueryLog['success'][] = $_query;
								} else{
									//unknown why mysql don't show correct error
									if($db->Errno == 1062 || $db->Errno == 0){
										$duplicate = true;
										$this->QueryLog['tableChanged'][] = $tableName;
									} else{
										$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n-- $_query --";
									}
									$success = false;
								}
							}
							if($success){
								$this->QueryLog['tableChanged'][] = $tableName . "\n<!-- $query -->";
							} else if($duplicate){
								if($db->query('RENAME TABLE ' . $db->escape($tableName) . ' TO ' . $db->escape($backupName))){
									$db->query($orgTable);
									$db->lock(array($tableName => 'write', $backupName => 'read'));
									foreach($alterQueries as $_query){
										if(trim($query) && !$db->query(trim($_query))){
											$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n-- $_query --";
										}
									}
									$db->query('INSERT IGNORE INTO ' . $db->escape($tableName) . ' SELECT * FROM ' . $db->escape($backupName));
									$db->unlock();
								}
							}
							$SearchTempTableKeys = $this->getKeysFromTable($tableName);
							if(isset($SearchTempTableKeys['_temp'])){
								$db->query(trim('ALTER TABLE ' . $db->escape($tableName) . ' DROP INDEX _temp'));
							}
						} else{
							//$this->QueryLog['tableExists'][] = $tableName;
						}

						$db->query('DROP TABLE IF EXISTS ' . $db->escape($tmpName));
					}
					break;
				case '1062':
					$this->QueryLog['entryExists'][] = $db->Errno . ' ' . $db->Error . "\n<!-- $query -->";
					return false;
				default:
					$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n-- $query --";
					return false;
			}
			return false;
		}
		return true;
	}

	/**
	 * returns log array for db-queries
	 * @return array
	 */
	function getQueryLog($specific = ''){
		return ($specific ? $this->QueryLog[$specific] : $this->QueryLog);
	}

	/**
	 * resets query log, this is done after each query file.
	 */
	function clearQueryLog(){
		foreach($this->QueryLog as &$cur){
			$cur = array();
		}
	}

	/**
	 * returns array with all installed languages
	 *
	 * @return array
	 */
	function getInstalledLanguages(){
		clearstatcache();

		//	Get all installed Languages
		$_installedLanguages = array();
		//	Look which languages are installed
		$_language_directory = dir($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_language");

		while(false !== ($entry = $_language_directory->read())) {
			if($entry != "." && $entry != ".."){
				if(is_dir($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_language/" . $entry)){
					$_installedLanguages[] = $entry;
				}
			}
		}
		$_language_directory->close();

		return $_installedLanguages;
	}

	/**
	 * This file sets another errorhandler - to make specific error-messages
	 *
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 * @param string $errcontext
	 */
	static function liveUpdateErrorHandler($errno, $errstr, $errfile, $errline, $errcontext){
		/* 		if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php')){
		  we_error_setHandleAll();
		  error_handler($errno, $errstr, $errfile, $errline, $errcontext);
		  } */

		$GLOBALS['liveUpdateError']["errorNr"] = $errno;
		$GLOBALS['liveUpdateError']["errorString"] = $errstr;
		$GLOBALS['liveUpdateError']["errorFile"] = $errfile;
		$GLOBALS['liveUpdateError']["errorLine"] = $errline;

//		ob_start('error_log');
//		var_dump($liveUpdateError);
//		ob_end_clean();
	}

}
