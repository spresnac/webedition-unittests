<?php

/**
 * webEdition CMS
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
//FIXME: remove this file almost complete; at least all DB queries. Replace by Update-Script calls on DB-Files.
class we_updater{

	static function replayUpdateDB(){
		include_once(WEBEDITION_PATH . 'liveUpdate/conf/conf.inc.php');
		$lf = new liveUpdateFunctions();
		$GLOBALS['we']['errorhandler']['sql'] = false;
		$d = dir(LIVEUPDATE_CLIENT_DOCUMENT_DIR . 'sqldumps');
		while(false !== ($entry = $d->read())) {
			if(substr($entry, -4) == '.sql'){
				$lf->executeQueriesInFiles(LIVEUPDATE_CLIENT_DOCUMENT_DIR . 'sqldumps/' . $entry);
			}
		}
		$d->close();
		$GLOBALS['we']['errorhandler']['sql'] = true;
		$entries = $lf->getQueryLog('error');
		if(!empty($entries)){
			t_e('Errors while updating tables', $entries);
		}
	}

	static function updateTables(){
		global $DB_WE;
		$db2 = new DB_WE();
		$tables = $db2->table_names(TBL_PREFIX . 'tblOwner');

		if(!empty($tables)){
			$DB_WE->query('SELECT * FROM ' . TBL_PREFIX . 'tblOwner');
			while($DB_WE->next_record()) {
				$table = $DB_WE->f("DocumentTable");
				if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
					$id = $DB_WE->f("fileID");
					if($table && $id){
						$Owners = ($DB_WE->f("OwnerID") && ($DB_WE->f("OwnerID") != $DB_WE->f("CreatorID"))) ? ("," . $DB_WE->f("OwnerID") . ",") : "";
						$CreatorID = $DB_WE->f("CreatorID") ? $DB_WE->f("CreatorID") : $_SESSION["user"]["ID"];
						$ModifierID = $DB_WE->f("ModifierID") ? $DB_WE->f("ModifierID") : $_SESSION["user"]["ID"];
						$db2->query("UPDATE " . $db2->escape($table) . " SET CreatorID=" . intval($CreatorID) . " , ModifierID=" . intval($ModifierID) . " , Owners='" . $db2->escape($Owners) . "' WHERE ID=" . intval($id));
						$db2->query('DELETE FROM ' . TBL_PREFIX . ' WHERE fileID=' . intval($id));
						@set_time_limit(30);
					}
				}
			}
			$DB_WE->query('DROP TABLE ' . TBL_PREFIX . 'tblOwner');
		}

		$DB_WE->query('UPDATE ' . CATEGORY_TABLE . ' SET Text=Category WHERE Text=""');
		$DB_WE->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT("/",Category) WHERE Path=""');

		//UPDATE old prefs
		$DB_WE->query('DROP TABLE IF EXISTS ' . PREFS_TABLE . '_old');
		if(count(getHash('SELECT * FROM ' . PREFS_TABLE . ' LIMIT 1', $GLOBALS['DB_WE'])) > 3){
			//make a backup
			$DB_WE->query('CREATE TABLE ' . PREFS_TABLE . '_old LIKE ' . PREFS_TABLE);
			$DB_WE->query('INSERT INTO ' . PREFS_TABLE . '_old SELECT * FROM ' . PREFS_TABLE);

			$DB_WE->query('DELETE FROM ' . PREFS_TABLE . ' WHERE userID=0');
			$DB_WE->query('SELECT * FROM ' . PREFS_TABLE . ' LIMIT 1');
			$queries = $DB_WE->getAll();
			$keys = array_keys($queries[0]);
			foreach($keys as $key){
				switch($key){
					case 'userID':
					case 'key':
					case 'value':
						continue;
					default:
						$GLOBALS['DB_WE']->delCol(PREFS_TABLE, $key);
				}
			}

			$GLOBALS['DB_WE']->query('DELETE FROM ' . PREFS_TABLE . ' WHERE `key`=""');
			foreach($queries as $q){
				we_user::writePrefs($q['userID'], $GLOBALS['DB_WE'], $q);
			}
		}
	}

	static function convertPerms(){
		global $DB_WE;
		$db_tmp = new DB_WE();
		$DB_WE->query('SELECT ID,username,Permissions FROM ' . USER_TABLE . ' WHERE Permissions NOT LIKE "%ADMINISTRATOR%"');
		while($DB_WE->next_record()) {
			$perms_slot = array();
			$pstr = $DB_WE->f("Permissions");
			$perms_slot["ADMINISTRATOR"] = $pstr[0];
			$perms_slot["PUBLISH"] = $pstr[1];
			if(count($perms_slot) > 0){
				$db_tmp->query('UPDATE ' . USER_TABLE . " SET Permissions='" . $db_tmp->escape(serialize($perms_slot)) . "' WHERE ID=" . intval($DB_WE->f("ID")));
			}
		}
	}

	static function fix_path(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query('SELECT ID,username,ParentID,Path FROM ' . USER_TABLE);
		while($db->next_record()) {
			@set_time_limit(30);
			$id = $db->f('ID');
			$pid = $db->f('ParentID');
			$path = '/' . $db->f("username");
			while($pid > 0) {
				$db2->query('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid));
				if($db2->next_record()){
					$path = '/' . $db2->f("username") . $path;
					$pid = $db2->f("ParentID");
				} else{
					$pid = 0;
				}
			}
			if($db->f('Path') != $path){
				$db2->query('UPDATE ' . USER_TABLE . " SET Path='" . $db2->escape($path) . "' WHERE ID=" . intval($id));
			}
		}
	}

	static function fix_icon(){
		$db = new DB_WE();
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user_alias.gif' WHERE Type=" . we_user::TYPE_ALIAS);
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='usergroup.gif' WHERE Type=" . we_user::TYPE_USER_GROUP);
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user.gif' WHERE Type=" . we_user::TYPE_USER);
	}

	static function fix_text(){
		$db = new DB_WE();
		$db->query('UPDATE ' . USER_TABLE . ' SET Text=username');
	}

	static function updateUnindexedCols($tab, $col){
		global $DB_WE;
		$DB_WE->query("SHOW COLUMNS FROM " . $DB_WE->escape($tab) . " LIKE '" . $DB_WE->escape($col) . "'");
		$query = array();
		while($DB_WE->next_record()) {
			if($DB_WE->f('Key') == ''){
				$query[] = 'ADD INDEX (' . $DB_WE->f('Field') . ')';
			}
		}
		if(count($query) > 0){
			$DB_WE->query('ALTER TABLE ' . $DB_WE->escape($tab) . ' ' . implode(', ', $query));
		}
	}

	static function updateUsers(){
		global $DB_WE;
		self::convertPerms();

		self::fix_path();
		self::fix_text();
		self::fix_icon();

		$DB_WE->query('UPDATE ' . USER_TABLE . " SET IsFolder=1 WHERE Type=" . we_user::TYPE_USER_GROUP);

		self::fix_icon();
		$GLOBALS['DB_WE']->query('SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND (value NOT LIKE "%_UTF-8%" OR value!="") AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $GLOBALS['DB_WE']->getAll(true);
		if(!empty($users)){
			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="ISO-8859-1" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
		}
		$GLOBALS['DB_WE']->query('SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND (value LIKE "%_UTF-8%") AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $GLOBALS['DB_WE']->getAll(true);
		if(!empty($users)){
			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="UTF-8" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value=REPLACE(value,"_UTF-8","") WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
		}
		$GLOBALS['DB_WE']->query('SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value="" AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $GLOBALS['DB_WE']->getAll(true);
		if(!empty($users)){
			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="UTF-8" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
			//$_SESSION['prefs'] = we_user::readPrefs($_SESSION['user']['ID'], $GLOBALS['DB_WE']);
		}
		$GLOBALS['DB_WE']->query('SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value=""');
		$users = $GLOBALS['DB_WE']->getAll(true);
		if(!empty($users)){
			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
		}
		$_SESSION['prefs'] = we_user::readPrefs($_SESSION['user']['ID'], $GLOBALS['DB_WE']);

		return true;
	}

	static function updateCustomers(){
		global $DB_WE;

		if(defined("CUSTOMER_TABLE")){
			if(!$GLOBALS['DB_WE']->isTabExist(CUSTOMER_ADMIN_TABLE)){
				$cols = array(
					"Name" => "VARCHAR(255) NOT NULL",
					"Value" => "TEXT NOT NULL"
				);

				$GLOBALS['DB_WE']->addTable(CUSTOMER_ADMIN_TABLE, $cols);

				$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('FieldAdds','');");
				$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('SortView','');");
				$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('Prefs','');");

				include(WE_MODULES_PATH . 'customer/weCustomerSettings.php');
				$settings = new weCustomerSettings();
				$settings->customer = new weCustomer();
				$fields = $settings->customer->getFieldsDbProperties();
				$_keys = array_keys($fields);
				foreach($_keys as $name){
					if(!$settings->customer->isProtected($name) && !$settings->customer->isProperty($name)){
						$settings->FieldAdds[$name]["type"] = "input";
						$settings->FieldAdds[$name]["default"] = "";
					}
				}
				$settings->save();
			}
		}
		return true;
	}

	static function updateScheduler(){
		if(defined("SCHEDULE_TABLE")){
			we_schedpro::check_and_convert_to_sched_pro();
		}
		return true;
	}

	static function updateObjectFilesX(){
		if(defined('OBJECT_X_TABLE')){
			$_db = new DB_WE();

			$_table = OBJECT_FILES_TABLE;

			$_db->query('SHOW TABLES LIKE "' . OBJECT_X_TABLE . '%"');	//note: _% ignores _, so escaping _ with \_ does the job
			$allTab = $_db->getAll(true);
			foreach($allTab as $_table){
				if($_table == OBJECT_FILES_TABLE){
					continue;
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Url')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_Url', 'VARCHAR(255) NOT NULL');
				} else{
					$GLOBALS['DB_WE']->addCol($_table, 'OF_Url', 'VARCHAR(255) NOT NULL', '  AFTER OF_Path  ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_TriggerID')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0');
				} else{
					$GLOBALS['DB_WE']->addCol($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0', '  AFTER OF_Url  ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_IsSearchable')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1');
				} else{
					$GLOBALS['DB_WE']->addCol($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1', ' AFTER OF_Published ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Charset')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL');
				} else{
					$GLOBALS['DB_WE']->addCol($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL', ' AFTER OF_IsSearchable ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_WebUserID')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL');
				} else{
					$GLOBALS['DB_WE']->addCol($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL', ' AFTER OF_Charset ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Language')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL');
				} else{
					$GLOBALS['DB_WE']->addCol($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL', ' AFTER OF_WebUserID ');
				}
				//add indices to all objects
				self::updateUnindexedCols($_table, 'object_%');
				$key = 'KEY OF_WebUserID (OF_WebUserID)';
				if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, $key)){
					$GLOBALS['DB_WE']->addKey($_table, $key);
				}
				$key = 'KEY published (OF_ID,OF_Published,OF_IsSearchable)';
				if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, $key)){
					$GLOBALS['DB_WE']->addKey($_table, $key);
				}
				$key = 'KEY OF_IsSearchable (OF_IsSearchable)';
				if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, $key)){
					$GLOBALS['DB_WE']->addKey($_table, $key);
				}
			}
		}
		return true;
	}

	static function updateVoting(){
		if(defined('VOTING_TABLE')){
			//this looks weird but means just :\"question inside the table
			$GLOBALS['DB_WE']->query('UPDATE ' . VOTING_TABLE . ' SET
			QASet=REPLACE(QASet,\'\\\\"\',\'"\'),
			QASetAdditions=REPLACE(QASetAdditions,\'\\\\"\',\'"\'),
			Scores=REPLACE(Scores,\'\\\\"\',\'"\'),
			Revote=REPLACE(Revote,\'\\\\"\',\'"\'),
			RevoteUserAgent=REPLACE(RevoteUserAgent,\'\\\\"\',\'"\'),
			LogData=REPLACE(LogData,\'\\\\"\',\'"\'),
			BlackList=REPLACE(BlackList,\'\\\\"\',\'"\')
			WHERE QASet LIKE \'%:\\\\\\\"question%\'');
		}
	}

	private static function updateLangLink(){
		if((!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")) || (!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)"))){
			//no unique def. found
			$db = $GLOBALS['DB_WE'];
			if($db->query('CREATE TEMPORARY TABLE tmpLangLink LIKE ' . LANGLINK_TABLE)){

				// copy links from documents or document-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . FILE_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . FILE_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . FILE_TABLE . ".Language AND " . LANGLINK_TABLE . ".IsObject = 0 AND " . LANGLINK_TABLE . ".DocumentTable = 'tblFile'");

				// copy links from objects or object-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblObjectFiles
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . OBJECT_FILES_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . OBJECT_FILES_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . OBJECT_FILES_TABLE . ".Language AND " . LANGLINK_TABLE . ".IsObject = 1");

				// copy links from doctypes to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . DOC_TYPES_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . DOC_TYPES_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . DOC_TYPES_TABLE . ".Language AND " . LANGLINK_TABLE . ".DocumentTable = 'tblDocTypes'");

				$db->query('TRUNCATE ' . LANGLINK_TABLE);
				if(!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)")){
					if($GLOBALS['DB_WE']->isKeyExistAtAll(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)")){
						$GLOBALS['DB_WE']->delKey(LANGLINK_TABLE, 'DID');
					}
					$GLOBALS['DB_WE']->addKey(LANGLINK_TABLE, 'UNIQUE KEY DID (DID,DLocale,IsObject,IsFolder,Locale,DocumentTable)');
				}
				if(!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")){
					if($GLOBALS['DB_WE']->isKeyExistAtAll(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")){
						$GLOBALS['DB_WE']->delKey(LANGLINK_TABLE, 'DLocale');
					}
					$GLOBALS['DB_WE']->addKey(LANGLINK_TABLE, 'UNIQUE KEY DLocale (DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable)');
				}

				// copy links from documents, document-folders and object-folders (to documents) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO " . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . FILE_TABLE . " WHERE tmpLangLink.LDID = " . FILE_TABLE . ".ID AND tmpLangLink.Locale = " . FILE_TABLE . ".Language AND tmpLangLink.IsObject = 0 AND tmpLangLink.DocumentTable = 'tblFile' ORDER BY tmpLangLink.ID DESC");

				// copy links from objects (to objects) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO " . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . OBJECT_FILES_TABLE . " WHERE tmpLangLink.LDID = " . OBJECT_FILES_TABLE . ".ID AND tmpLangLink.Locale = " . OBJECT_FILES_TABLE . ".Language AND tmpLangLink.IsObject = 1 ORDER BY tmpLangLink.ID DESC");

				// copy links from doctypes (to doctypes) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO " . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . DOC_TYPES_TABLE . " WHERE tmpLangLink.LDID = " . DOC_TYPES_TABLE . ".ID AND tmpLangLink.Locale = " . DOC_TYPES_TABLE . ".Language AND tmpLangLink.DocumentTable = 'tblDocTypes' ORDER BY tmpLangLink.ID DESC");
			} else{
				t_e('no rights to create temp-table');
			}
		}
	}

	static function convertTemporaryDoc(){
		if($GLOBALS['DB_WE']->isColExist(TEMPORARY_DOC_TABLE, 'ID')){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE Active=0');
			$GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblFile" WHERE DocTable  LIKE "%tblFile"');
			$GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblObjectFiles" WHERE DocTable LIKE "%tblObjectFiles"');
			$GLOBALS['DB_WE']->delCol(TEMPORARY_DOC_TABLE, 'ID');
			$GLOBALS['DB_WE']->delKey(TEMPORARY_DOC_TABLE, 'PRIMARY');
			$GLOBALS['DB_WE']->addKey(TEMPORARY_DOC_TABLE, 'PRIMARY KEY ( `DocumentID` , `DocTable` , `Active` )');
		}
	}

	private static function getAllIDFromQuery($sql){
		$db = $GLOBALS['DB_WE'];
		$db->query($sql);
		return $db->getAll(true);
	}

	static function fixInconsistentTables(){
		$db = $GLOBALS['DB_WE'];
		$del = self::getAllIDFromQuery('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND DID NOT IN(SELECT ID FROM ' . FILE_TABLE . ')');
		$del = array_merge($del, self::getAllIDFromQuery('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblTemplates" AND DID NOT IN(SELECT ID FROM ' . TEMPLATES_TABLE . ')'));

		if(!empty($del)){
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE CID IN (' . implode(',', $del) . ')');
		}

		$db->query('SELECT ID FROM ' . CONTENT_TABLE . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLE . ')');
		$del = $db->getAll(true);
		if(!empty($del)){
			$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (' . implode(',', $del) . ')');
		}

		if(defined('SCHEDULE_TABLE')){
			$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName != "we_objectFile" AND DID NOT IN (SELECT ID FROM ' . FILE_TABLE . ')');

			if(defined('OBJECT_FILES_TABLE')){
				$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName = "we_objectFile" AND DID NOT IN (SELECT ID FROM ' . OBJECT_FILES_TABLE . ')');
			}
		}
		//FIXME: clean customerfilter
		//FIXME: clean history
		//FIXME: clean inconsistent objects
	}

	static function updateGlossar(){
		//FIXME: remove after 7.0
		foreach($GLOBALS['weFrontendLanguages'] as $lang){
			$cache = new weGlossaryCache($lang);
			$cache->write();
		}
	}

	function doUpdate(){
		self::replayUpdateDB();

		self::updateTables();
		self::updateUsers();
		self::updateObjectFilesX();
		self::updateScheduler();
		self::updateVoting();
		self::convertTemporaryDoc();
		self::updateLangLink();
		self::fixInconsistentTables();
		self::updateGlossar();
		self::replayUpdateDB();
	}

}