<?php

/**
 * webEdition CMS
 *
 * $Rev: 5563 $
 * $Author: mokraemer $
 * $Date: 2013-01-13 20:50:09 +0100 (Sun, 13 Jan 2013) $
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
abstract class weBackupUtil{

	static function getRealTableName($table){
		$table = strtolower($table);
		$match = array();
		if(preg_match("|tblobject_([0-9]*)$|", $table, $match)){
			return (isset($_SESSION['weS']['weBackupVars']['tables']['tblobject_']) ?
					$_SESSION['weS']['weBackupVars']['tables']['tblobject_'] . $match[1] :
					false);
		}

		return (isset($_SESSION['weS']['weBackupVars']['tables'][$table]) ?
				$_SESSION['weS']['weBackupVars']['tables'][$table] :
				false);
	}

	static function getDefaultTableName($table){

		$match = array();
		if(defined('OBJECT_X_TABLE') && preg_match("|^" . OBJECT_X_TABLE . "([0-9]*)$|i", $table, $match)){
			if(isset($_SESSION['weS']['weBackupVars']['tables']['tblobject_'])){
				$_max = f('SELECT MAX(ID) AS MaxTableID FROM ' . OBJECT_TABLE, 'MaxTableID', new DB_WE());
				if($match[1] <= $_max){
					return 'tblobject_' . $match[1];
				}
			}

			return false;
		}


//$_def_table = array_search($table,$_SESSION['weS']['weBackupVars']['tables']);
		foreach($_SESSION['weS']['weBackupVars']['tables'] as $_key => $_value){
			if(strtolower($table) == strtolower($_value)){
				$_def_table = $_key;
			}
		}

// return false or default table name
		if(!empty($_def_table)){
			return $_def_table;
		}

		return false;
	}

	static function setBackupVar($name, $value){
		$_SESSION['weS']['weBackupVars'][$name] = $value;
	}

	static function getDescription($table, $prefix){
		switch($table){
			case CONTENT_TABLE:
				return g_l('backup', '[' . $prefix . '_content]');
			case FILE_TABLE:
				return g_l('backup', '[' . $prefix . '_files]');
			case LINK_TABLE:
				return g_l('backup', '[' . $prefix . '_links]');
			case TEMPLATES_TABLE:
				return g_l('backup', '[' . $prefix . '_templates]');
			case TEMPORARY_DOC_TABLE:
				return g_l('backup', '[' . $prefix . '][temporary_data]');
			case HISTORY_TABLE:
				return g_l('backup', '[' . $prefix . '][history_data]');
			case INDEX_TABLE:
				return g_l('backup', '[' . $prefix . '_indexes]');
			case DOC_TYPES_TABLE:
				return g_l('backup', "[" . $prefix . '_doctypes]');
			case (defined('USER_TABLE') ? USER_TABLE : 'USER_TABLE'):
				return g_l('backup', "[" . $prefix . '_user_data]');
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				return g_l('backup', "[" . $prefix . '_customer_data]');
			case (defined('SHOP_TABLE') ? SHOP_TABLE : 'SHOP_TABLE'):
				return g_l('backup', "[" . $prefix . '_shop_data]');
			case (defined('PREFS_TABLE') ? PREFS_TABLE : 'PREFS_TABLE'):
				return g_l('backup', "[" . $prefix . '_prefs]');
			case (defined('BACKUP_TABLE') ? BACKUP_TABLE : 'BACKUP_TABLE'):
				return g_l('backup', "[" . $prefix . '_extern_data]');
			case (defined('BANNER_CLICKS_TABLE') ? BANNER_CLICKS_TABLE : 'BANNER_CLICKS_TABLE'):
				return g_l('backup', "[" . $prefix . '_banner_data]');
			default:
				return g_l('backup', '[working]');
		}
	}

	static function getImportPercent(){
		if(isset($_SESSION['weS']['weBackupVars']['files_to_delete_count'])){
			$rest1 = intval($_SESSION['weS']['weBackupVars']['files_to_delete_count']) - count($_SESSION['weS']['weBackupVars']['files_to_delete']);
			$rest2 = intval($_SESSION['weS']['weBackupVars']['files_to_delete_count']);
		} else{
			$rest1 = 0;
			$rest2 = 0;
		}

		$percent = round(((float)
			(intval($_SESSION['weS']['weBackupVars']['offset'] + $rest1) /
			intval($_SESSION['weS']['weBackupVars']['offset_end'] + $rest2))) * 100, 2);

		return (intval($percent) > 100 ? 100 : (intval($percent) < 0 ? 0 : $percent));
	}

	static function getProgressJS($percent, $description){
		return
			'if(top.busy && top.busy.setProgressText && top.busy.setProgress){
								top.busy.setProgressText("current_description", "' . $description . '");
								top.busy.setProgress(' . $percent . ');
							}';
	}

	static function getExportPercent(){
		$all = intval($_SESSION['weS']['weBackupVars']['row_count']);
		$done = intval($_SESSION['weS']['weBackupVars']['row_counter']);

		if(isset($_SESSION['weS']['weBackupVars']['extern_files'])){
			$all += intval($_SESSION['weS']['weBackupVars']['extern_files_count']);
			$done += intval($_SESSION['weS']['weBackupVars']['extern_files_count']) - count($_SESSION['weS']['weBackupVars']['extern_files']);
		}

		$percent = round(($done / $all) * 100, ($all > 50000 ? 2 : 1));
		return (intval($percent) < 0 ? 0 : (intval($percent) > 100 ? 100 : $percent));
	}

	static function canImportBinary($id, $path){

		if(!empty($id) && $_SESSION['weS']['weBackupVars']['options']['backup_binary']){
			return true;
		}

		if(empty($id) && ($path == WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php' || $path == WE_INCLUDES_DIR . 'conf/we_conf_language.inc.php') && $_SESSION['weS']['weBackupVars']['handle_options']['settings']){
			return true;
		}

		if(empty($id) && $_SESSION['weS']['weBackupVars']['options']['backup_extern'] && ($path != WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php' || $path != WE_INCLUDES_DIR . 'conf/we_conf_language.inc.php')){
			return true;
		}

		if(empty($id) && strpos($path, WE_MODULES_DIR . 'spellchecker') === 0 && $_SESSION['weS']['weBackupVars']['handle_options']['spellchecker']){
			return true;
		}

		return false;
	}

	static function canImportVersion($id, $path){
		return (!empty($id) && stristr($path, VERSION_DIR) && $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']);
	}

	static function exportFile($file, $fh){
		$bin = weContentProvider::getInstance('weBinary', 0);
		$bin->Path = $file;

		weContentProvider::binary2file($bin, $fh, false);
	}

	static function exportFiles($to, $files){
		$count = count($files);

		if(($fh = fopen($to, 'ab'))){
			for($i = 0; $i < $count; $i++){
				$file_to_export = $files[$i];
				self::exportFile($file_to_export, $fh);
			}
			fclose($fh);
		}
	}

	static function getNextTable(){
		if(!isset($_SESSION['weS']['weBackupVars']['allTables'])){
			$_db = new DB_WE();
			$_SESSION['weS']['weBackupVars']['allTables'] = $_db->table_names();
		}
// get all table names from database
		$_tables = $_SESSION['weS']['weBackupVars']['allTables'];
		$_do = true;

		do{
			if(++$_SESSION['weS']['weBackupVars']['current_table_id'] < count($_tables)){
// get real table name from database
				$_table = $_tables[$_SESSION['weS']['weBackupVars']['current_table_id']]['table_name'];

				$_def_table = self::getDefaultTableName($_table);

				if($_def_table !== false){

					$_do = false;

					$_SESSION['weS']['weBackupVars']['current_table'] = $_table;
				}
			} else{
				$_SESSION['weS']['weBackupVars']['current_table'] = false;
				$_do = false;
			}
		} while($_do);

		return $_SESSION['weS']['weBackupVars']['current_table'];
	}

	static function getCurrentTable(){
		return $_SESSION['weS']['weBackupVars']['current_table'];
	}

	static function addLog($log){
		if(isset($_SESSION['weS']['weBackupVars']['backup_log_data'])){
			$_SESSION['weS']['weBackupVars']['backup_log_data'] .= '[' . date('d-M-Y H:i:s', time()) . '] ' . $log . "\r\n";
		}
	}

	static function writeLog(){
		if($_SESSION['weS']['weBackupVars']['backup_log_data'] == ''){
			return;
		}
		if($_SESSION['weS']['weBackupVars']['backup_log']){
			weFile::save($_SESSION['weS']['weBackupVars']['backup_log_file'], $_SESSION['weS']['weBackupVars']['backup_log_data'], 'ab');
		}
		$_SESSION['weS']['weBackupVars']['backup_log_data'] = '';
	}

	static function getHttpLink($server, $url, $port = '', $username = '', $password = ''){
		return getServerProtocol(true) . (($username && $password) ? "$username:$password@" : '') . $server . ($port != '' ? ':' . $port : '') . $url;
	}

	static function getFormat($file, $iscompr = 0){
		$_part = weFile::loadPart($file, 0, 512, $iscompr);

		if(preg_match('|<\?xml |i', $_part)){
			return 'xml';
		} else if(stripos($_part, 'create table') !== false){
			return 'sql';
		}

		return 'unknown';
	}

	static function getXMLImportType($file, $iscompr = 0, $end_off = 0){
		$_found = 'unknown';
		$_try = 0;
		$_count = 30;
		$_part_len = 16384;
		$_part_skip_len = 204800;

		if($end_off == 0){
			$end_off = self::getEndOffset($file, $iscompr);
		}

		$_start = $end_off - $_part_len;

		$_part = weFile::loadPart($file, 0, $_part_len, $iscompr);

		if(stripos($_part, '<webEdition') === false){
			return 'unknown';
		}
		$_hasbinary = false;
		while($_found == 'unknown' && $_try < $_count) {
			if(preg_match('/.*<webEdition.*type="backup".*>/', $_part)){
				return 'backup';
			} elseif(preg_match('/<we:(document|template|class|object|info|navigation)/i', $_part)){
				return 'weimport';
			} elseif(stripos($_part, '<we:table') !== false){
				return 'backup';
			} elseif(stripos($_part, '<we:binary') !== false){
				$_hasbinary = true;
			} elseif(stripos($_part, '<customer') !== false){
				return 'customer';
			}

			$_part = weFile::loadPart($file, $_start, $_part_len, $iscompr);

			$_start = $_start - $_part_skip_len;

			$_try++;
		}

		if($_found == 'unknown' && $_hasbinary){
			return 'weimport';
		}

		return $_found;
	}

	static function getEndOffset($filename, $iscompressed){

		$end = 0;

		if($iscompressed == 0){

			$fh = fopen($filename, 'rb');
			if($fh){
				fseek($fh, 0, SEEK_END);
				$end = ftell($fh);
				fclose($fh);
			}
		} else{

			$fh = gzopen($filename, 'rb');
			while(!gzeof($fh)) {
				gzread($fh, 16768);
			}
			$end = gztell($fh);
			gzclose($fh);
		}
		return $end;
	}

	static function hasNextTable(){

		$_current_id = $_SESSION['weS']['weBackupVars']['current_table_id'];
		$_current_id++;

		if(!isset($_SESSION['weS']['weBackupVars']['allTables'])){
			$_db = new DB_WE();
			$_SESSION['weS']['weBackupVars']['allTables'] = $_db->table_names();
		}
// get all table names from database
		$_tables = $_SESSION['weS']['weBackupVars']['allTables'];

		if($_current_id < count($_tables)){
			$_table = $_tables[$_current_id]['table_name'];
			if(self::getDefaultTableName($_table) === false){
				return false;
			}

			return true;
		}

		return false;
	}

}
