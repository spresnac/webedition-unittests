<?php

/**
 * webEdition CMS
 *
 * $Rev: 5964 $
 * $Author: mokraemer $
 * $Date: 2013-03-15 12:41:02 +0100 (Fri, 15 Mar 2013) $
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
class weBackupPreparer{

	function checkFilePermission(){

		if(!is_writable($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR)){
			weBackupUtil::addLog('Error: Can\'t write to ' . $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR);
			return false;
		}

		if(!is_writable($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/')){
			weBackupUtil::addLog('Error: Can\'t write to ' . $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/');
			return false;
		}
		return true;
	}

	function prepare(){

		if(!self::checkFilePermission()){
			return false;
		}


		$_SESSION['weS']['weBackupVars'] = array(
			'options' => array(),
			'handle_options' => array(),
			'offset' => 0,
			'current_table' => '',
			'backup_steps' => getPref('BACKUP_STEPS'),
			'backup_log' => (isset($_REQUEST['backup_log']) && $_REQUEST['backup_log']) ? $_REQUEST['backup_log'] : 0,
			'backup_log_data' => '',
			'backup_log_file' => $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'data/lastlog.php',
			'limits' => array(
				'mem' => we_convertIniSizes(ini_get('memory_limit')),
				'exec' => ini_get('max_execution_time'),
			),
		);

		weBackupPreparer::getOptions($_SESSION['weS']['weBackupVars']['options'], $_SESSION['weS']['weBackupVars']['handle_options']);
		$_SESSION['weS']['weBackupVars']['tables'] = weBackupPreparer::getTables($_SESSION['weS']['weBackupVars']['handle_options']);

		if($_SESSION['weS']['weBackupVars']['backup_steps'] == 0){
			$_SESSION['weS']['weBackupVars']['backup_steps'] = weBackupWizard::getAutoSteps();
		}

		if($_SESSION['weS']['weBackupVars']['backup_log']){
			weFile::save($_SESSION['weS']['weBackupVars']['backup_log_file'], "<?php exit();?>\r\n");
		}

		return true;
	}

	function prepareExport(){

		if(!weBackupPreparer::prepare()){
			return false;
		}
		we_updater::fixInconsistentTables();

		$_SESSION['weS']['weBackupVars']['protect'] = (isset($_REQUEST['protect']) && $_REQUEST['protect']) ? $_REQUEST['protect'] : 0;

		$_SESSION['weS']['weBackupVars']['filename'] = ((isset($_REQUEST['filename']) && $_REQUEST['filename']) ? ($_REQUEST['filename']) : '');
		$_SESSION['weS']['weBackupVars']['backup_file'] = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_SESSION['weS']['weBackupVars']['filename'];
		$_SESSION['weS']['weBackupVars']['options']['compress'] = (isset($_REQUEST['compress']) && $_REQUEST['compress'] && weFile::hasCompression($_REQUEST['compress'])) ? $_REQUEST['compress'] : 0;

		$_SESSION['weS']['weBackupVars']['current_table_id'] = -1;

		if($_SESSION['weS']['weBackupVars']['options']['backup_extern']){
			$_SESSION['weS']['weBackupVars']['extern_files'] = array();
			self::getFileList($_SESSION['weS']['weBackupVars']['extern_files']);
			$_SESSION['weS']['weBackupVars']['extern_files_count'] = count($_SESSION['weS']['weBackupVars']['extern_files']);
		}
		$_SESSION['weS']['weBackupVars']['limits'] = array(
			'mem' => we_convertIniSizes(ini_get('memory_limit')),
			'exec' => ini_get('max_execution_time'),
		);


		$_SESSION['weS']['weBackupVars']['row_counter'] = 0;
		$_SESSION['weS']['weBackupVars']['row_count'] = 0;

		$db = new DB_WE();
		$db->query('SHOW TABLE STATUS');
		while($db->next_record()) {
			// fix for object tables
			//if(in_array($db->f('Name'),$_SESSION['weS']['weBackupVars']['tables'])) {
			if(($name = weBackupUtil::getDefaultTableName($db->f('Name'))) !== false){
				$_SESSION['weS']['weBackupVars']['row_count'] += $db->f('Rows');
				$_SESSION['weS']['weBackupVars']['avgLen'][$name] = $db->f('Avg_row_length');
			}
		}

		weFile::save($_SESSION['weS']['weBackupVars']['backup_file'], ($_SESSION['weS']['weBackupVars']['protect'] && !$_SESSION['weS']['weBackupVars']['options']['compress'] ? weBackup::weXmlExImProtectCode : '') . weXMLExIm::getHeader('','backup'));

		return true;
	}

	function prepareImport(){

		if(!self::prepare()){
			return false;
		}

		$_SESSION['weS']['weBackupVars']['backup_file'] = self::getBackupFile();
		if($_SESSION['weS']['weBackupVars']['backup_file'] === false){
			return false;
		}

		$_offset = strlen(weBackup::weXmlExImProtectCode);
		$_SESSION['weS']['weBackupVars']['offset'] = (weFile::loadLine($_SESSION['weS']['weBackupVars']['backup_file'], 0, ($_offset + 1)) == weBackup::weXmlExImProtectCode) ? $_offset : 0;

		$_SESSION['weS']['weBackupVars']['options']['compress'] = weFile::isCompressed($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset']) ? 1 : 0;
		if($_SESSION['weS']['weBackupVars']['options']['compress']){
			$_SESSION['weS']['weBackupVars']['backup_file'] = self::makeCleanGzip($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset']);
			we_util_File::insertIntoCleanUp($_SESSION['weS']['weBackupVars']['backup_file'], time() + (8 * 3600)); //valid for 8 hours
			$_SESSION['weS']['weBackupVars']['offset'] = 0;
		}

		$_SESSION['weS']['weBackupVars']['options']['format'] = weBackupUtil::getFormat($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress']);

		if($_SESSION['weS']['weBackupVars']['options']['format'] != 'xml' && $_SESSION['weS']['weBackupVars']['options']['format'] != 'sql'){
			return false;
		}

		$_SESSION['weS']['weBackupVars']['offset_end'] = weBackupUtil::getEndOffset($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress']);

		if($_SESSION['weS']['weBackupVars']['options']['format'] == 'xml'){
			$_SESSION['weS']['weBackupVars']['options']['xmltype'] = weBackupUtil::getXMLImportType($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress'], $_SESSION['weS']['weBackupVars']['offset_end']);
			if($_SESSION['weS']['weBackupVars']['options']['xmltype'] != 'backup'){
				return false;
			}
		}

		$_SESSION['weS']['weBackupVars']['encoding'] = self::getEncoding($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress']);
		$_SESSION['weS']['weBackupVars']['weVersion'] = self::getWeVersion($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress']);

		if($_SESSION['weS']['weBackupVars']['handle_options']['core']){
			weBackupPreparer::clearTemporaryData('tblFile');
			$_SESSION['weS']['weBackupVars']['files_to_delete'] = self::getFileLists();
			$_SESSION['weS']['weBackupVars']['files_to_delete_count'] = count($_SESSION['weS']['weBackupVars']['files_to_delete']);
		}

		if($_SESSION['weS']['weBackupVars']['handle_options']['versions']
			|| $_SESSION['weS']['weBackupVars']['handle_options']['core']
			|| $_SESSION['weS']['weBackupVars']['handle_options']['object']
			|| $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']
		){
			weBackupPreparer::clearVersionData();
		}

		if($_SESSION['weS']['weBackupVars']['handle_options']['object']){
			weBackupPreparer::clearTemporaryData('tblObjectFiles');
		}

		return true;
	}

	function getOptions(&$options, &$handle_options){

		$options['backup_extern'] = (isset($_REQUEST['handle_extern']) && $_REQUEST['handle_extern']) ? 1 : 0;
		$options['convert_charset'] = (isset($_REQUEST["convert_charset"]) && $_REQUEST["convert_charset"]) ? 1 : 0;
		$options['compress'] = (isset($_REQUEST['compress']) && $_REQUEST['compress']) ? 1 : 0;
		$options['backup_binary'] = (isset($_REQUEST['handle_binary']) && $_REQUEST['handle_binary']) ? 1 : 0;
		$options['rebuild'] = (isset($_REQUEST['rebuild']) && $_REQUEST['rebuild']) ? 1 : 0;

		$options['export2server'] = (isset($_REQUEST['export_server']) && $_REQUEST['export_server']) ? 1 : 0;
		$options['export2send'] = (isset($_REQUEST['export_send']) && $_REQUEST['export_send']) ? 1 : 0;

		$options['do_import_after_backup'] = (isset($_REQUEST['do_import_after_backup']) && $_REQUEST['do_import_after_backup']) ? 1 : 0;


		$handle_options['user'] = (isset($_REQUEST['handle_user']) && $_REQUEST['handle_user']) ? 1 : 0;
		$handle_options['customer'] = (isset($_REQUEST['handle_customer']) && $_REQUEST['handle_customer']) ? 1 : 0;
		$handle_options['shop'] = (isset($_REQUEST['handle_shop']) && $_REQUEST['handle_shop']) ? 1 : 0;
		$handle_options['workflow'] = (isset($_REQUEST['handle_workflow']) && $_REQUEST['handle_workflow']) ? 1 : 0;
		$handle_options['todo'] = (isset($_REQUEST['handle_todo']) && $_REQUEST['handle_todo']) ? 1 : 0;
		$handle_options['newsletter'] = (isset($_REQUEST['handle_newsletter']) && $_REQUEST['handle_newsletter']) ? 1 : 0;
		$handle_options['temporary'] = (isset($_REQUEST['handle_temporary']) && $_REQUEST['handle_temporary']) ? 1 : 0;
		$handle_options['history'] = (isset($_REQUEST['handle_history']) && $_REQUEST['handle_history']) ? 1 : 0;
		$handle_options['banner'] = (isset($_REQUEST['handle_banner']) && $_REQUEST['handle_banner']) ? 1 : 0;
		$handle_options['core'] = (isset($_REQUEST['handle_core']) && $_REQUEST['handle_core']) ? 1 : 0;
		$handle_options['object'] = (isset($_REQUEST['handle_object']) && $_REQUEST['handle_object']) ? 1 : 0;
		$handle_options['schedule'] = (isset($_REQUEST['handle_schedule']) && $_REQUEST['handle_schedule']) ? 1 : 0;
		$handle_options['settings'] = (isset($_REQUEST['handle_settings']) && $_REQUEST['handle_settings']) ? 1 : 0;
		$handle_options['configuration'] = (isset($_REQUEST['handle_configuration']) && $_REQUEST['handle_configuration']) ? 1 : 0;
		$handle_options['export'] = (isset($_REQUEST['handle_export']) && $_REQUEST['handle_export']) ? 1 : 0;
		$handle_options['voting'] = (isset($_REQUEST['handle_voting']) && $_REQUEST['handle_voting']) ? 1 : 0;
		$handle_options['spellchecker'] = (isset($_REQUEST['handle_spellchecker']) && $_REQUEST['handle_spellchecker']) ? 1 : 0;
		$handle_options['versions'] = (isset($_REQUEST['handle_versions']) && $_REQUEST['handle_versions']) ? 1 : 0;
		$handle_options['versions_binarys'] = (isset($_REQUEST['handle_versions_binarys']) && $_REQUEST['handle_versions_binarys']) ? 1 : 0;

		$handle_options['tools'] = array();

		foreach($_REQUEST as $_k => $_val){
			if(stripos($_k, "handle_tool_") === 0){
				$_tool = str_replace("handle_tool_", '', $_k);
				if(weToolLookup::isTool($_tool)){
					$handle_options['tools'][] = $_tool;
				}
			}
		}
		$handle_options['spellchecker'] = (isset($_REQUEST['handle_spellchecker']) && $_REQUEST['handle_spellchecker']) ? 1 : 0;

		// exception for sql imports
		$handle_options['glossary'] = (isset($_REQUEST['handle_glossary']) && $_REQUEST['handle_glossary']) ? 1 : 0;
		// exception for sql imports
		$handle_options['backup'] = $options['backup_extern'];
		if($options['convert_charset']){
			$handle_options['settings'] = 0;
			$handle_options['spellchecker'] = 0;
		}
	}

	function getTables($options){
		include(WE_INCLUDES_PATH . 'we_exim/backup/weTableMap.inc.php');

		$tables = array();
		foreach($options as $group => $enabled){
			if($enabled && isset($tableMap[$group])){
				$tables = array_merge($tables, $tableMap[$group]);
			}
		}

		if(!empty($options['tools'])){
			foreach($options['tools'] as $_tool){
				$tables = array_merge($tables, weToolLookup::getBackupTables($_tool));
			}
		}

		return $tables;
	}

	function getBackupFile(){

		$backup_select = (isset($_REQUEST['backup_select']) && $_REQUEST['backup_select']) ? $_REQUEST['backup_select'] : '';
		$we_upload_file = (isset($_FILES['we_upload_file']) && $_FILES['we_upload_file']) ? $_FILES['we_upload_file'] : '';

		if($backup_select){
			return $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . $backup_select;
		} else if($we_upload_file && ($we_upload_file != 'none')){

			$_SESSION['weS']['weBackupVars']['options']['upload'] = 1;

			if(empty($_FILES['we_upload_file']['tmp_name']) || $_FILES['we_upload_file']['error']){
				return false;
			}

			$filename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_FILES['we_upload_file']['name'];

			if(move_uploaded_file($_FILES['we_upload_file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_FILES['we_upload_file']['name'])){
				we_util_File::insertIntoCleanUp($filename, time());
				return $filename;
			}
		}

		return null;
	}

	function getExternalFiles(){
		$list = array();
		weBackupPreparer::getFileList($list, TEMPLATES_PATH, true, false);
		return $list;
	}

	function getFileLists(){
		$list = array();
		weBackupPreparer::getFileList($list, TEMPLATES_PATH, true, false);
		weBackupPreparer::getFileList($list, $_SERVER['DOCUMENT_ROOT'] . weNavigationCache::CACHEDIR, true, false);
		self::getSiteFiles($list);
		return $list;
	}

	function getFileList(array &$list, $dir = '', $with_dirs = false, $rem_doc_root = true){
		$dir = ($dir == '' ? $_SERVER['DOCUMENT_ROOT'] : $dir);
		if(!is_readable($dir) || !is_dir($dir)){
			return false;
		}
		$thumbDir = trim(WE_THUMBNAIL_DIRECTORY, '/');

		$d = dir($dir);
		while(false !== ($entry = $d->read())) {
			switch($entry){
				case '.':
				case '..':
				case 'CVS':
				case 'webEdition':
				case 'sql_dumps':
				case '.project':
				case '.trustudio.dbg.php':
				case 'LanguageChanges.csv':
				case $thumbDir:
					continue;
				default:
					$file = $dir . '/' . $entry;
					if(!weBackupPreparer::isPathExist(str_replace($_SERVER['DOCUMENT_ROOT'], '', $file))){
						if(is_dir($file)){
							if($with_dirs){
								weBackupPreparer::addToFileList($list, $file, $rem_doc_root);
							}
							weBackupPreparer::getFileList($list, $file, $with_dirs, $rem_doc_root);
						} else{
							weBackupPreparer::addToFileList($list, $file, $rem_doc_root);
						}
					} elseif(is_dir($file)){
						weBackupPreparer::getFileList($list, $file, $with_dirs, $rem_doc_root);
					}
			}
		}
		$d->close();
	}

	function addToFileList(array &$list, $file, $rem_doc_root = true){
		if($rem_doc_root){
			$list[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
		} else{
			$list[] = $file;
		}
	}

	function getSiteFiles(array &$out){
		global $DB_WE;

		$list = array();
		weBackupPreparer::getFileList($list, $_SERVER['DOCUMENT_ROOT'] . SITE_DIR, true, false);
		foreach($list as $file){
			//don't use f/getHash since RAM usage
			$DB_WE->query('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape(str_replace($_SERVER['DOCUMENT_ROOT'] . rtrim(SITE_DIR, '/'), '', $file)) . '"', false, true);
			$DB_WE->next_record();
			switch($DB_WE->f('ContentType')){
				case 'image/*':
				case 'application/*':
				case 'application/x-shockwave-flash':
					continue;
				default:
					$out[] = $file;
			}
		}
	}

	function clearTemporaryData($docTable){
		global $DB_WE;
		$DB_WE->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocTable="' . stripTblPrefix($docTable) . '"');
		$DB_WE->query('TRUNCATE TABLE ' . NAVIGATION_TABLE);
		$DB_WE->query('TRUNCATE TABLE ' . NAVIGATION_RULE_TABLE);
		$DB_WE->query('TRUNCATE TABLE ' . HISTORY_TABLE);
	}

	function clearVersionData(){
		global $DB_WE;
		$DB_WE->query('TRUNCATE TABLE ' . VERSIONS_TABLE . ';');
		$path = $_SERVER['DOCUMENT_ROOT'] . VERSION_DIR;
		if(($dir = opendir($path))){
			while(($file = readdir($dir))) {
				if(!is_dir($file) && $file != "." && $file != ".." && $file != "dummy"){
					unlink($path . $file);
				}
			}
			closedir($dir);
		}
	}

	function isPathExist($path){
		global $DB_WE;

		return ((f('SELECT 1 AS a FROM ' . FILE_TABLE . " WHERE Path='" . $DB_WE->escape($path) . "'", 'a', $DB_WE) == '1')
			|| (f('SELECT 1 AS a FROM ' . TEMPLATES_TABLE . " WHERE Path='" . $DB_WE->escape($path) . "'", 'a', $DB_WE) == '1'));
	}

	static function getEncoding($file, $iscompressed){

		if(!empty($file)){
			$data = weFile::loadPart($file, 0, 256, $iscompressed);
			$match = array();
			$trenner = "[\040|\n|\t|\r]*";
			$pattern = "%(encoding" . $trenner . "=" . $trenner . "[\"|\'|\\\\]" . $trenner . ")([^\'\">\040? \\\]*)%";

			if(preg_match($pattern, $data, $match)){
				if(strtoupper($match[2]) != 'ISO-8859-1'){
					return 'UTF-8';
				}
			}
		}

		return 'ISO-8859-1';
	}

	static function getWeVersion($file, $iscompressed){
		if(!empty($file)){
			$data = weFile::loadPart($file, 0, 256, $iscompressed);
			$match = array();
			$trenner = "[\040|\n|\t|\r]*";
			$pattern = "%webEdition" . $trenner . "version" . $trenner . "=" . $trenner . "[\"|\'|\\\\]" . $trenner . "([^\'\">\040? \\\]*)%";

			if(preg_match($pattern, $data, $match)){
				return $match[1];
			}
		}

		return -1;
	}

	function isOtherXMLImport($format){

		switch($format){
			case 'weimport':
				if(we_hasPerm('WXML_IMPORT')){
					return we_html_element::jsElement('
							if(confirm("' . str_replace('"','\'',g_l('backup', '[import_file_found]') . ' \n\n' . g_l('backup', '[import_file_found_question]')) . '")){
								top.opener.top.we_cmd("import");
								top.close();
							} else {
								top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=2";
							}');
				} else{
					return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[import_file_found]'), we_message_reporting::WE_MESSAGE_WARNING) . '
							top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=2";');
				}
			case 'customer':
				return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[customer_import_file_found]'), we_message_reporting::WE_MESSAGE_WARNING) . '
						top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=2";');
			default:
				return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[format_unknown]'), we_message_reporting::WE_MESSAGE_WARNING) . '
						top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=2";');
		}
	}

	function getErrorMessage(){
		$_mess = '';

		if(empty($_SESSION['weS']['weBackupVars']['backup_file'])){
			if(isset($_SESSION['weS']['weBackupVars']['options']['upload'])){
				$maxsize = getUploadMaxFilesize();
				$_mess = sprintf(g_l('backup', '[upload_failed]'), round($maxsize / (1024 * 1024), 3) . "MB");
			} else{
				$_mess = g_l('backup', '[file_missing]');
			}
		} else if(!is_readable($_SESSION['weS']['weBackupVars']['backup_file'])){

			$_mess = g_l('backup', '[file_not_readable]');
		} else if($_SESSION['weS']['weBackupVars']['options']['format'] != 'xml' && $_SESSION['weS']['weBackupVars']['options']['format'] != 'sql'){

			$_mess = g_l('backup', '[format_unknown]');
		} else if($_SESSION['weS']['weBackupVars']['options']['xmltype'] != 'backup'){

			return weBackupPreparer::isOtherXMLImport($_SESSION['weS']['weBackupVars']['options']['xmltype']);
		} else if($_SESSION['weS']['weBackupVars']['options']['compress'] && !weFile::hasGzip()){

			$_mess = g_l('backup', '[cannot_split_file_ziped]');
		} else{
			$_mess = g_l('backup', '[unspecified_error]');
		}

		if($_SESSION['weS']['weBackupVars']['backup_log']){
			weBackupUtil::addLog('Error: ' . $_mess);
		}

		return we_html_element::jsElement(we_message_reporting::getShowMessageCall($_mess, we_message_reporting::WE_MESSAGE_ERROR) . '
					top.body.location = "' . WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php?pnt=body&step=2";');
	}

	function makeCleanGzip($gzfile, $offset){

		$file = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . weFile::getUniqueId();
		$fs = @fopen($gzfile, "rb");

		if($fs){
			if(fseek($fs, $offset, SEEK_SET) == 0){
				$fp = @fopen($file, "wb");
				if($fp){
					do{
						$data = fread($fs, 8192);
						if(strlen($data) == 0)
							break;
						fwrite($fp, $data);
					} while(true);
					fclose($fp);
				}
				else{
					fclose($fs);
					return false;
				}
			}
			fclose($fs);
		} else{
			return false;
		}

		return $file;
	}

}
