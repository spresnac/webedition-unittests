<?php

/**
 * webEdition CMS
 *
 * $Rev: 5890 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 22:11:18 +0100 (Mon, 25 Feb 2013) $
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
class we_user{

	const TYPE_USER = 0;
	const TYPE_USER_GROUP = 1;
	const TYPE_ALIAS = 2;

	// Name of the class => important for reconstructing the class from outside the class
	var $ClassName = __CLASS__;
	// In this array are all storagable class variables
	var $persistent_slots = array('ID', 'Type', 'ParentID', 'Salutation', 'First', 'Second', 'Address', 'HouseNo', 'City', 'PLZ', 'State', 'Country', 'Tel_preselection', 'Telephone', 'Fax', 'Fax_preselection', 'Handy', 'Email', 'username', 'passwd', 'clearpasswd', 'Text', 'Path', 'Permissions', 'ParentPerms', 'Description', 'Alias', 'Icon', 'IsFolder', 'CreatorID', 'CreateDate', 'ModifierID', 'ModifyDate', 'Ping', 'workSpace', 'workSpaceDef', 'workSpaceTmp', 'workSpaceNav', 'workSpaceNwl', 'workSpaceObj', 'ParentWs', 'ParentWst', 'ParentWsn', 'ParentWso', 'ParentWsnl', 'altID', 'LoginDenied', 'UseSalt');
	// Name of the Object that was createt from this class
	var $Name = '';
	// ID from the database record
	var $ID = 0;
	// database table in which the object is stored
	var $Table = USER_TABLE;
	// Database Object
	var $DB_WE;
	// Parent identificator
	var $ParentID = 0;
	// Flag which indicates which kind of user is 0-user;1-group;2-owner group;3 - alias
	var $Type = self::TYPE_USER;
	// Flag which indicates if user is group
	var $IsFolder = 0;
	// Salutation
	var $Salutation = '';
	// User first name
	var $First = '';
	// User second name
	var $Second = '';
	// Address
	var $Address = '';
	// House number
	var $HouseNo = '';
	// City
	var $City = '';
	// ZIP Code
	var $PLZ = '';
	// Country
	var $Country = '';
	// Telephone preselection
	var $Tel_preselection = '';
	// Telephone
	var $Telephone = '';
	// Fax preselection
	var $Fax_preselection = '';
	// Fax
	var $Fax = '';
	// Cell phone
	var $Handy = '';
	// Email
	var $Email = '';
	// Username
	var $username = '';
	// User password (md5 salted)
	var $passwd = '';
	// User password
	var $clearpasswd = '';
	// User permissions
	var $Permissions = '';
	// Flag which indicated if user inherits permissions from parent
	var $ParentPerms = 0;
	// Description
	var $Description = '';
	// User Prefrences
	var $Preferences = array(
		'use_jupload' => 0,
	);
	var $Text = '';
	var $Path = '';
	var $Alias = '';
	var $Icon = 'user.gif';
	var $CreatorID = '';
	var $CreateDate = '';
	var $ModifierID = '';
	var $ModifyDate = '';
	// Ping flag
	var $Ping = 0;
	// Documents workspaces
	var $workSpace = '';
	// Default documents workspaces
	var $workSpaceDef = '';
	// Templpates workspaces
	var $workSpaceTmp = '';
	// Navigation workspaces
	var $workSpaceNav = '';
	// Objects workspaces
	var $workSpaceObj = '';
	// Newsletter workspaces
	var $workSpaceNwl = '';
	// Flag which indicated if user inherits files workspaces from parent
	var $ParentWs = 0;
	// Flag which indicated if user inherits templates workspaces from parent
	var $ParentWst = 0;
	// Flag which indicated if user inherits templates workspaces from parent
	var $ParentWsn = 0;
	// Flag which indicated if user inherits objetcs workspaces from parent
	var $ParentWso = 0;
	// Flag which indicated if user inherits newsletters workspaces from parent
	var $ParentWsnl = 0;
	var $LoginDenied = 0;
	// Flag which indicated if user inherits templates workspaces from parent
	var $initExt = 0;

	/*
	 * ADDITIONAL
	 */
	// Workspace array
	var $workspaces = array(
		FILE_TABLE => array(),
		TEMPLATES_TABLE => array(),
		NAVIGATION_TABLE => array(),
	);
	// Workspace array
	var $workspaces_defaults = array(
		FILE_TABLE => array(),
		TEMPLATES_TABLE => array(),
		NAVIGATION_TABLE => array(),
	);
	// Aliases array
	var $aliases = array();
	// Permissions headers array
	var $permissions_main_titles = array();
	// Permissions values array
	var $permissions_slots = array();
	// Permissions titles
	var $permissions_titles = array();
	// Extensions array
	var $extensions_slots = array();
	// Preferences array
	var $preference_slots = array('sizeOpt', 'weWidth', 'weHeight', 'usePlugin', 'autostartPlugin', 'promptPlugin', 'Language', 'BackendCharset', 'seem_start_file', 'seem_start_type', 'seem_start_weapp', 'editorSizeOpt', 'editorWidth', 'editorHeight', 'editorFontname', 'editorFontsize', 'editorFont', 'default_tree_count', 'force_glossary_action', 'force_glossary_check', 'cockpit_amount_columns', 'cockpit_amount_last_documents', 'cockpit_rss_feed_url', 'use_jupload', 'editorMode');
	var $UseSalt = 1;

	// Constructor
	function __construct(){
		$GLOBALS['editor_reloaded'] = false;

		$this->Name = 'user_' . md5(uniqid(__FILE__, true));

		$this->DB_WE = new DB_WE;

		if(defined('OBJECT_TABLE')){
			$this->workspaces[OBJECT_FILES_TABLE] = array();
		}
		if(defined('NEWSLETTER_TABLE')){
			$this->workspaces[NEWSLETTER_TABLE] = array();
		}

		if(defined('OBJECT_TABLE')){
			$this->workspaces_defaults[OBJECT_FILES_TABLE] = array();
		}
		if(defined('NEWSLETTER_TABLE')){
			$this->workspaces_defaults[NEWSLETTER_TABLE] = array();
		}

		foreach($this->preference_slots as $val){
			$this->Preferences[$val] = null;
		}

		$this->initType(self::TYPE_USER);
	}

	function initType($typ, $ext = 0){
		$this->Type = $typ;
		switch($typ){
			case self::TYPE_ALIAS:
				$this->Icon = 'user_alias.gif';
				break;
			case self::TYPE_USER_GROUP:
				$this->Icon = 'usergroup.gif';
				break;
			default:
				$this->Icon = 'user.gif';
				break;
		}
		$this->mapPermissions();
		if($ext){
			$this->initExt = $ext;
			foreach($this->extensions_slots as $k => $v)
				$this->extensions_slots[$k]->init($this);
		}
	}

	// Intialize the class

	function initFromDB($id){
		$ret = false;
		if($id){
			$this->DB_WE->query('SELECT * FROM ' . USER_TABLE . ' WHERE ID=' . intval($id));
			if($this->DB_WE->next_record()){
				$this->ID = $id;
				$this->getPersistentSlotsFromDB();
				$this->getPreferenceSlotsFromDB();
				$ret = true;
			}
			$this->loadWorkspaces();
			$this->mapPermissions();
		}
		return $ret;
	}

	function savePersistentSlotsInDB(){
		$this->ModDate = time();
		$tableInfo = $this->DB_WE->metadata($this->Table);
		$useSalt = 0;
		if($this->clearpasswd !== ''){
			$this->passwd = self::makeSaltedPassword($useSalt, $this->username, $this->clearpasswd);
		}

		$updt = array();
		foreach($tableInfo as $t){
			$fieldName = $t['name'];
			if($fieldName == 'UseSalt' && $useSalt > 0){
				$val = $useSalt;
			} else{
				$val = isset($this->$fieldName) ? $this->$fieldName : 0;
			}
			if($fieldName != 'ID'){
				if($fieldName == 'editorFontname' && $this->Preferences['editorFont'] == '0'){
					$val = 'none';
				} elseif($fieldName == 'editorFontsize' && $this->Preferences['editorFont'] == '0'){
					$val = '-1';
				}
				if($fieldName !== 'passwd' || $val !== ''){
					$updt [$fieldName] = $val;
				}
			}
		}
		$this->DB_WE->query(($this->ID ? 'UPDATE ' : 'INSERT INTO ') . $this->DB_WE->escape($this->Table) . ' SET ' . we_database_base::arraySetter($updt) . ($this->ID ? ' WHERE ID=' . intval($this->ID) : ''));
		if(!$this->ID){
			$this->ID = $this->DB_WE->getInsertId();
		}
	}

	function createAccount(){
		if(defined('MESSAGING_SYSTEM')){
			require_once(WE_MODULES_PATH . 'messaging/messaging_interfaces.inc.php');
			msg_create_folders($this->ID);
		}
	}

	function removeAccount(){
		if(defined('MESSAGING_SYSTEM')){
			$this->DB_WE->query('DELETE FROM ' . MSG_ADDRBOOK_TABLE . ' WHERE UserID = ' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MESSAGES_TABLE . ' WHERE UserID = ' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_TODO_TABLE . ' WHERE UserID = ' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_TODOHISTORY_TABLE . ' WHERE UserID = ' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID = ' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_ACCOUNTS_TABLE . ' WHERE UserID = ' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_SETTINGS_TABLE . ' WHERE UserID = ' . $this->ID);
		}
	}

	function getPersistentSlotsFromDB(){
		$tableInfo = $this->DB_WE->metadata($this->Table);
		$tmp = getHash('SELECT * FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ID), $this->DB_WE);
		foreach($tableInfo as $t){
			$fieldName = $t['name'];
			if(in_array($fieldName, $this->persistent_slots)){
				$this->$fieldName = $tmp[$fieldName];
			}
		}
	}

	function saveToDB(){
		$db_tmp = new DB_WE();
		$isnew = $this->ID ? false : true;
		if($this->Type == self::TYPE_USER_GROUP && $this->ID != 0){
			$ppath = ($this->ParentID == 0 ? '/' : $this->getPath($this->ParentID));
			$dpath = $this->getPath($this->ID);
			if(preg_match('|' . $dpath . '|', $ppath))
				return -5;
		}
		if($this->Type == self::TYPE_ALIAS){
			$foo = getHash('SELECT ID,username FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->Alias), $this->DB_WE);
			$uorginal = $foo['ID'];
			$search = true;
			$ount = 0;
			$try_name = '@' . $foo['username'];
			$try_text = $foo['username'];
			while($search) {
				$this->DB_WE->query('SELECT username FROM ' . USER_TABLE . ' WHERE ID!=' . intval($this->ID) . ' AND ID!=' . intval($uorginal) . " AND username='" . $this->DB_WE->escape($try_name) . "'");
				if(!$this->DB_WE->next_record()){
					$search = false;
				} else{
					$ount++;
					$try_name = $try_name . '_' . $ount;
				}
			}
			$this->username = $try_name;
			$this->Text = $try_text;
		} else{
			$this->Text = $this->username;
		}
		$this->IsFolder = ($this->Type == self::TYPE_USER_GROUP ? 1 : 0);
		$this->Path = $this->getPath($this->ID);
		$oldpath = $this->Path;
		$this->saveWorkspaces();
		$this->savePermissions();
		if($isnew){
			$this->CreatorID = $_SESSION['user']['ID'];
			$this->ModifierID = $_SESSION['user']['ID'];
			$this->CreateDate = time();
			$this->ModifyDate = time();
		} else{
			$this->ModifierID = $_SESSION['user']['ID'];
			$this->ModifyDate = time();
		}
		$this->savePersistentSlotsInDB();
		$this->createAccount();
		if($oldpath != '' && $oldpath != '/'){
			$this->DB_WE->query('SELECT ID,username FROM ' . USER_TABLE . " WHERE Path LIKE '" . $this->DB_WE->escape($oldpath) . "%'");
			while($this->DB_WE->next_record()) {
				$db_tmp->query('UPDATE ' . USER_TABLE . " SET Path='" . $this->getPath($this->DB_WE->f("ID")) . "' WHERE ID=" . $this->DB_WE->f("ID"));
			}
		}
		$this->savePreferenceSlotsInDB($isnew);

		$_REQUEST['uid'] = $this->ID;

		return $this->saveToSession();
	}

	function saveToSession(){
		if($this->ID != $_SESSION['user']['ID']){
			return '';
		}

		$save_javascript =
			'var _multiEditorreload = false;' .
			$this->rememberPreference(isset($this->Preferences['Language']) ? $this->Preferences['Language'] : null, 'Language') .
			$this->rememberPreference(isset($this->Preferences['BackendCharset']) ? $this->Preferences['BackendCharset'] : null, 'BackendCharset') .
			$this->rememberPreference(isset($this->Preferences['default_tree_count']) ? $this->Preferences['default_tree_count'] : null, 'default_tree_count');

		if(isset($this->Preferences['seem_start_type'])){
			switch($this->Preferences['seem_start_type']){
				case 'cockpit':
					$save_javascript .=
						$this->rememberPreference(0, 'seem_start_file') .
						$this->rememberPreference('cockpit', 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp');
					break;
				case 'object':
					$save_javascript .=
						$this->rememberPreference(isset($this->Preferences['seem_start_object']) ? $this->Preferences['seem_start_object'] : 0, 'seem_start_file') .
						$this->rememberPreference('object', 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp');
					break;
				case 'weapp':
					$save_javascript .=
						$this->rememberPreference(isset($this->Preferences['seem_start_weapp']) ? $this->Preferences['seem_start_weapp'] : '', 'seem_start_weapp') .
						$this->rememberPreference('weapp', 'seem_start_type') .
						$this->rememberPreference(0, 'seem_start_file');
					break;
				default:
					$save_javascript .=
						$this->rememberPreference(isset($this->Preferences['seem_start_document']) ? $this->Preferences['seem_start_document'] : 0, 'seem_start_file') .
						$this->rememberPreference('document', 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp');
			}
		}
		$save_javascript .=
			$this->rememberPreference(isset($this->Preferences['sizeOpt']) ? $this->Preferences['sizeOpt'] : null, 'sizeOpt') .
			$this->rememberPreference(isset($this->Preferences['weWidth']) ? $this->Preferences['weWidth'] : null, 'weWidth') .
			$this->rememberPreference(isset($this->Preferences['weHeight']) ? $this->Preferences['weHeight'] : null, 'weHeight') .
			$this->rememberPreference(isset($this->Preferences['editorMode']) ? $this->Preferences['editorMode'] : null, 'editorMode') .
			$this->rememberPreference(isset($this->Preferences['editorFont']) ? $this->Preferences['editorFont'] : null, 'editorFont') .
			$this->rememberPreference(isset($this->Preferences['editorFontname']) ? $this->Preferences['editorFontname'] : null, 'editorFontname') .
			$this->rememberPreference(isset($this->Preferences['editorFontsize']) ? $this->Preferences['editorFontsize'] : null, 'editorFontsize') .
			$this->rememberPreference(isset($this->Preferences['editorSizeOpt']) ? $this->Preferences['editorSizeOpt'] : null, 'editorSizeOpt') .
			$this->rememberPreference(isset($this->Preferences['editorWidth']) ? $this->Preferences['editorWidth'] : null, 'editorWidth') .
			$this->rememberPreference(isset($this->Preferences['editorHeight']) ? $this->Preferences['editorHeight'] : null, 'editorHeight') .
			$this->rememberPreference(isset($this->Preferences['force_glossary_action']) ? $this->Preferences['force_glossary_action'] : null, 'force_glossary_action') .
			$this->rememberPreference(isset($this->Preferences['force_glossary_check']) ? $this->Preferences['force_glossary_check'] : null, 'force_glossary_check');

		return $save_javascript;
	}

	function mapPermissions(){
		$this->permissions_main_titles = array();
		$this->permissions_slots = array();
		$this->permissions_titles = array();
		$permissions = unserialize($this->Permissions);

		$entries = weToolLookup::getPermissionIncludes();

		$d = dir(WE_USERS_MODULE_PATH . 'perms');
		while(($file = $d->read())) {
			if(substr($file, 0, 9) == 'we_perms_'){
				$entries[] = WE_USERS_MODULE_PATH . 'perms/' . $file;
			}
		}
		$d->close();

		foreach($entries as $entry){

			$perm_group_name = '';
			$perm_values = array();
			$perm_titles = array();
			$perm_group_title = array();
			include($entry);
			if(!($perm_group_name == 'administrator' && $this->Type != self::TYPE_USER)){
				if($perm_group_name){
					if(!isset($this->permissions_main_titles[$perm_group_name])){
						$this->permissions_main_titles[$perm_group_name] = '';
					}
					if(!isset($this->permissions_slots[$perm_group_name])){
						$this->permissions_slots[$perm_group_name] = array();
					}
					if(!isset($this->permissions_titles[$perm_group_name])){
						$this->permissions_titles[$perm_group_name] = '';
					}
					if(is_array($perm_values[$perm_group_name])){
						foreach($perm_values[$perm_group_name] as $k => $v){
							$set = false;
							if(is_array($permissions)){
								foreach($permissions as $pk => $pv){
									if($v == $pk){
										$set = true;
										$this->permissions_slots[$perm_group_name][$pk] = $pv;
									}
								}
							}
							if(!$set){
								$this->permissions_slots[$perm_group_name][$v] = (is_array($perm_defaults[$perm_group_name]) ? $perm_defaults[$perm_group_name][$v] : 0);
							}
						}
					}

					$this->permissions_main_titles[$perm_group_name] = $perm_group_title[$perm_group_name];

					if(is_array($perm_titles[$perm_group_name])){
						foreach($perm_titles[$perm_group_name] as $key => $val){
							$this->permissions_titles[$perm_group_name][$key] = $val;
						}
					}
				}
			}
		}
	}

	function setPermissions(){
		foreach($this->perm_branches as $val){
			foreach($val as $k => $v){
				$this->Permissions[$k] = $this->permissions_slots[$v];
			}
		}
	}

	function setPermission($perm_name, $perm_value){
		foreach($this->permissions_slots as $key => $val){
			foreach($val as $k => $v){
				if($perm_name == $k){
					$this->permissions_slots[$key][$k] = $perm_value;
				}
			}
		}
	}

	function savePermissions(){
		$permissions = array();
		foreach($this->permissions_slots as $key => $val){
			foreach($val as $k => $v){
				$permissions[$k] = $v;
			}
		}
		$this->Permissions = serialize($permissions);
	}

	function loadWorkspaces(){
		if($this->workSpace){
			$this->workspaces[FILE_TABLE] = makeArrayFromCSV($this->workSpace);
		}
		if($this->workSpaceTmp){
			$this->workspaces[TEMPLATES_TABLE] = makeArrayFromCSV($this->workSpaceTmp);
		}
		if($this->workSpaceNav){
			$this->workspaces[NAVIGATION_TABLE] = makeArrayFromCSV($this->workSpaceNav);
		}
		if(defined('OBJECT_TABLE') && $this->workSpaceObj){
			$this->workspaces[OBJECT_FILES_TABLE] = makeArrayFromCSV($this->workSpaceObj);
		}
		if(defined('NEWSLETTER_TABLE') && $this->workSpaceNwl){
			$this->workspaces[NEWSLETTER_TABLE] = makeArrayFromCSV($this->workSpaceNwl);
		}

		if($this->workSpaceDef){
			$this->workspaces_defaults[FILE_TABLE] = makeArrayFromCSV($this->workSpaceDef);
		}
	}

	function saveWorkspaces(){
		foreach($this->workspaces as $k => $v){
			$new_array = array();
			foreach($v as $key => $val)
				if($val != 0){
					$new_array[] = $this->workspaces[$k][$key];
				}
			$this->workspaces[$k] = $new_array;
		}

		$this->workSpace = makeCSVFromArray($this->workspaces[FILE_TABLE], true, ',');
		$this->workSpaceTmp = makeCSVFromArray($this->workspaces[TEMPLATES_TABLE], true, ',');
		$this->workSpaceNav = makeCSVFromArray($this->workspaces[NAVIGATION_TABLE], true, ',');
		if(defined('OBJECT_TABLE')){
			$this->workSpaceObj = makeCSVFromArray($this->workspaces[OBJECT_FILES_TABLE], true, ',');
		}
		if(defined('NEWSLETTER_TABLE')){
			$this->workSpaceNwl = makeCSVFromArray($this->workspaces[NEWSLETTER_TABLE], true, ',');
		}
		foreach($this->workspaces_defaults as $k => $v){
			$new_array = array();
			foreach($v as $key => $val){
				if($val != 0){
					$new_array[] = $this->workspaces_defaults[$k][$key];
				}
			}
			$this->workspaces_defaults[$k] = $new_array;
		}
		$this->workSpaceDef = (empty($this->workspaces[FILE_TABLE]) ?
				'' :
				makeCSVFromArray($this->workspaces_defaults[FILE_TABLE], true, ','));

		// if no workspaces are set, take workspaces from creator
		if(empty($this->workSpace)){
			$_uws = get_ws(FILE_TABLE, true);
			if(!empty($_uws)){
				$this->workSpace = $_uws;
				$this->workspaces[FILE_TABLE] = makeArrayFromCSV($_uws);
			}
		}
		if(empty($this->workSpaceTmp)){
			$_uws = get_ws(TEMPLATES_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceTmp = $_uws;
				$this->workspaces[TEMPLATES_TABLE] = makeArrayFromCSV($_uws);
			}
		}
		if(empty($this->workSpaceNav)){
			$_uws = get_ws(NAVIGATION_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceNav = makeArrayFromCSV($_uws);
				$this->workspaces[NAVIGATION_TABLE] = makeArrayFromCSV($_uws);
			}
		}

		if(defined('OBJECT_FILES_TABLE') && empty($this->workSpaceObj)){
			$_uws = get_ws(OBJECT_FILES_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceObj = makeArrayFromCSV($_uws);
				$this->workspaces[OBJECT_FILES_TABLE] = makeArrayFromCSV($_uws);
			}
		}
		if(defined('NEWSLETTER_TABLE') && empty($this->workSpaceNwl)){
			$_uws = get_ws(NEWSLETTER_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceNwl = makeArrayFromCSV($_uws);
				$this->workspaces[NEWSLETTER_TABLE] = makeArrayFromCSV($_uws);
			}
		}
	}

	function getPreferenceSlotsFromDB(){
		$tmp = self::readPrefs($this->ID, $this->DB_WE);
		$this->Preferences = array_intersect_key($tmp, array_flip($this->preference_slots));
	}

	function setPreference($name, $value){
		if(in_array($name, $this->preference_slots)){
			$this->Preferences[$name] = $value;
		}
	}

	function savePreferenceSlotsInDB($isnew = false){
		if($this->Type != self::TYPE_USER){
			return;
		}

		$this->ModDate = time();
		$updt = array('userID' => intval($this->ID));
		foreach($this->preference_slots as $fieldName){
			switch($fieldName){
				case 'editorFontsize':
				case 'editorFontname':
					if($this->Preferences['editorFont'] != '1'){
						$this->Preferences[$fieldName] = '-1';
					}
				default:
					$updt[$fieldName] = $this->Preferences[$fieldName];
			}
		}
		if($isnew){
			$updt['userID'] = intval($this->ID);
			$updt['FileFilter'] = '0';
			$updt['openFolders_tblFile'] = '';
			$updt['openFolders_tblTemplates'] = '';
			$updt['DefaultTemplateID'] = '0';
		}
		self::writePrefs(intval($this->ID), $this->DB_WE, $updt);
		$_SESSION["prefs"] = ($_SESSION["prefs"]["userID"] == intval($this->ID) ? self::readPrefs(intval($this->ID), $this->DB_WE) : $_SESSION["prefs"]);
	}

	function rememberPreference($settingvalue, $settingname){
		$save_javascript = '';
		if(isset($settingvalue) && ($settingvalue != null)){
			switch($settingname){
				case 'Language':
					$_SESSION['prefs']['Language'] = $settingvalue;

					if($settingvalue != $GLOBALS['WE_LANGUAGE']){
						$save_javascript .= "
							if (top.frames[0]) {
								top.frames[0].location.reload();
							}

							if (parent.frames[0]) {
								parent.frames[0].location.reload();
							}

							// Tabs Module User
							if (top.content.user_resize.user_right.user_editor.user_edheader) {
								top.content.user_resize.user_right.user_editor.user_edheader.location = top.content.user_resize.user_right.user_editor.user_edheader.location +'?tab='+top.content.user_resize.user_right.user_editor.user_edheader.activeTab;
							}

							// Editor Module User
							if (top.content.user_resize.user_right.user_editor.user_properties) {
								top.content.user_resize.user_right.user_editor.user_properties.location = top.content.user_resize.user_right.user_editor.user_properties.location +'?tab=" . abs($_REQUEST['tab']) . "&perm_branch='+top.content.user_resize.user_right.user_editor.user_properties.opened_group;
							}

							// Save Module User
							if (top.content.user_resize.user_right.user_editor.user_edfooter) {
								top.content.user_resize.user_right.user_editor.user_edfooter.location.reload();
							}
							if (top.opener.top.header) {
								top.opener.top.header.location.reload();
							}

							// reload all frames of an editor
							// reload current document => reload all open Editors on demand
							var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
							for (frameId in _usedEditors) {

								if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
									_usedEditors[frameId].setEditorReloadAllNeeded(true);
									_usedEditors[frameId].setEditorIsActive(true);

								} else {
									_usedEditors[frameId].setEditorReloadAllNeeded(true);
								}
							}
							_multiEditorreload = true;
							";
					}
					break;
				case 'BackendCharset':
					$_SESSION['prefs']['BackendCharset'] = $settingvalue;

					if($settingvalue != $GLOBALS['WE_BACKENDCHARSET']){
						$save_javascript .= "
if (top.frames[0]) {
	top.frames[0].location.reload();
}

if (parent.frames[0]) {
	parent.frames[0].location.reload();
}

// Tabs Module User
if (top.content.user_resize.user_right.user_editor.user_edheader) {
	top.content.user_resize.user_right.user_editor.user_edheader.location = top.content.user_resize.user_right.user_editor.user_edheader.location +'?tab='+top.content.user_resize.user_right.user_editor.user_edheader.activeTab;
}

// Editor Module User
if (top.content.user_resize.user_right.user_editor.user_properties) {
	top.content.user_resize.user_right.user_editor.user_properties.location = top.content.user_resize.user_right.user_editor.user_properties.location +'?tab=" . abs($_REQUEST['tab']) . "&perm_branch='+top.content.user_resize.user_right.user_editor.user_properties.opened_group;
}

// Save Module User
if (top.content.user_resize.user_right.user_editor.user_edfooter) {
	top.content.user_resize.user_right.user_editor.user_edfooter.location.reload();
}
if (top.opener.top.header) {
	top.opener.top.header.location.reload();
}

// reload all frames of an editor
// reload current document => reload all open Editors on demand
var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
for (frameId in _usedEditors) {

	if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
		_usedEditors[frameId].setEditorIsActive(true);

	} else {
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
	}
}
_multiEditorreload = true;";
					}
					break;

				case 'seem_start_type':
					switch($settingvalue){
						case 'cockpit':
							$_SESSION['prefs']['seem_start_file'] = 0;
							$_SESSION['prefs']['seem_start_type'] = 'cockpit';
							break;
						case 'object':
							$_SESSION['prefs']['seem_start_file'] = $_REQUEST['seem_start_object'];
							$_SESSION['prefs']['seem_start_type'] = 'object';
							break;
						case 'weapp':
							$_SESSION['prefs']['seem_start_weapp'] = $_REQUEST['seem_start_weapp'];
							$_SESSION['prefs']['seem_start_type'] = 'weapp';
							break;
						default:
							$_SESSION['prefs']['seem_start_file'] = $_REQUEST['seem_start_document'];
							$_SESSION['prefs']['seem_start_type'] = 'document';
							break;
					}
					break;

				case 'sizeOpt':
					if($settingvalue == 0){
						$_SESSION['prefs']['weWidth'] = 0;
						$_SESSION['prefs']['weHeight'] = 0;
						$_SESSION['prefs']['sizeOpt'] = 0;
					} else if(($settingvalue == 1) && (isset($_POST['weWidth']) && is_numeric($_POST['weWidth'])) && (isset($_POST['weHeight']) && is_numeric($_POST['weHeight']))){
						$_SESSION['prefs']['sizeOpt'] = 1;
					}
					break;

				case 'weWidth':
					if($_SESSION['prefs']['sizeOpt'] == 1){
						$_generate_java_script = false;

						if($_SESSION['prefs']['weWidth'] != $settingvalue){
							$_generate_java_script = true;
						}

						$_SESSION['prefs']['weWidth'] = $settingvalue;

						if($_generate_java_script){
							$save_javascript .= '
								top.opener.top.resizeTo(' . $settingvalue . ', ' . $_POST['weHeight'] . ');
								top.opener.top.moveTo((screen.width / 2) - ' . ($settingvalue / 2) . ', (screen.height / 2) - ' . ($_POST['weHeight'] / 2) . ');
							';
						}
					}
					break;

				case 'weHeight':
					if($_SESSION['prefs']['sizeOpt'] == 1){
						$_SESSION['prefs']['weHeight'] = $settingvalue;
					}
					break;


				case 'editorMode':
					$_SESSION['prefs']['editorMode'] = $settingvalue;
					break;


				case 'editorFont':
					if($settingvalue == 0){
						$_SESSION['prefs']['editorFontname'] = 'none';
						$_SESSION['prefs']['editorFontsize'] = -1;
						$_SESSION['prefs']['editorFont'] = 0;
					} else if(($settingvalue == 1) && isset($_POST['editorFontname']) && isset($_POST['editorFontsize'])){
						$_SESSION['prefs']['editorFont'] = 1;
					}

					$save_javascript .= "
if ( !_multiEditorreload ) {
	var _usedEditors =  top.opener.top.weEditorFrameController.getEditorsInUse();

		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == \"" . TEMPLATES_TABLE . "\" || " . (defined("OBJECT_TABLE") ? " _usedEditors[frameId].getEditorEditorTable() == \"" . OBJECT_FILES_TABLE . "\" || " : "") . " _usedEditors[frameId].getEditorEditorTable() == \"" . FILE_TABLE . "\") &&
				_usedEditors[frameId].getEditorEditPageNr() == " . WE_EDITPAGE_CONTENT . " ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;";
					break;

				case 'editorFontname':
					if($_SESSION['prefs']['editorFont'] == 1){
						$_SESSION['prefs']['editorFontname'] = $settingvalue;
					}
					break;

				case 'editorFontsize':
					if($_SESSION['prefs']['editorFont'] == 1){
						$_SESSION['prefs']['editorFontsize'] = $settingvalue;
					}
					break;

				case 'editorSizeOpt':
					if($settingvalue == 0){
						$_SESSION['prefs']['editorWidth'] = 0;
						$_SESSION['prefs']['editorHeight'] = 0;
						$_SESSION['prefs']['editorSizeOpt'] = 0;
					} else if(($settingvalue == 1) && isset($_POST['editorWidth']) && isset($_POST['editorHeight'])){
						$_SESSION['prefs']['editorSizeOpt'] = 1;
					}

					if(!$GLOBALS['editor_reloaded']){
						$GLOBALS['editor_reloaded'] = true;

						$save_javascript .= "
if ( !_multiEditorreload ) {
	var _usedEditors =  top.opener.top.weEditorFrameController.getEditorsInUse();

		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == \"" . TEMPLATES_TABLE . "\" || " . (defined("OBJECT_TABLE") ? " _usedEditors[frameId].getEditorEditorTable() == \"" . OBJECT_FILES_TABLE . "\" || " : "") . " _usedEditors[frameId].getEditorEditorTable() == \"" . FILE_TABLE . "\") &&
				_usedEditors[frameId].getEditorEditPageNr() == " . WE_EDITPAGE_CONTENT . " ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;";
					}
					break;

				case 'editorWidth':
					if($_SESSION['prefs']['editorSizeOpt'] == 1){
						$_SESSION['prefs']['editorWidth'] = $settingvalue;
					}
					break;

				case 'editorHeight':
					if($_SESSION['prefs']['editorSizeOpt'] == 1){
						$_SESSION['prefs']['editorHeight'] = $settingvalue;
					}
					break;

				case 'default_tree_count':
					$_SESSION['prefs']['default_tree_count'] = $settingvalue;
					break;

				case 'force_glossary_check':
					$_SESSION['prefs']['force_glossary_check'] = $settingvalue;
					break;

				case 'force_glossary_action':
					$_SESSION['prefs']['force_glossary_action'] = $settingvalue;
					break;

				case 'cockpit_amount_columns':
					$_SESSION['prefs']['cockpit_amount_columns'] = $settingvalue;
					break;

				default:
					break;
			}
		} else{

			switch($settingname){

				case 'editorFont':
					$_SESSION['prefs']['editorFontname'] = 'none';
					$_SESSION['prefs']['editorFontsize'] = -1;
					$_SESSION['prefs']['editorFont'] = 0;

					$save_javascript .= "
if ( !_multiEditorreload ) {
	var _usedEditors =  top.opener.top.weEditorFrameController.getEditorsInUse();

		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == \"" . TEMPLATES_TABLE . "\" || " . (defined("OBJECT_TABLE") ? " _usedEditors[frameId].getEditorEditorTable() == \"" . OBJECT_FILES_TABLE . "\" || " : "") . " _usedEditors[frameId].getEditorEditorTable() == \"" . FILE_TABLE . "\") &&
				_usedEditors[frameId].getEditorEditPageNr() == " . WE_EDITPAGE_CONTENT . " ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;";
					break;

				case 'force_glossary_check':
					$_SESSION['prefs']['force_glossary_check'] = 0;
					break;

				case 'force_glossary_action':
					$_SESSION['prefs']['force_glossary_action'] = 0;
					break;

				default:
					break;
			}
		}

		return $save_javascript;
	}

	function preserveState($tab, $sub_tab){
		switch($tab){
			case 0:
				foreach($this->persistent_slots as $pkey => $pval){
					$obj = $this->Name . '_' . $pval;
					if(isset($_POST[$obj])){
						$this->$pval = $_POST[$obj];
					}
				}

				if($this->Type == self::TYPE_ALIAS){
					$this->ParentPerms = (isset($_POST[$this->Name . '_ParentPerms'])) ? 1 : 0;
					$this->ParentWs = (isset($_POST[$this->Name . '_ParentWs'])) ? 1 : 0;
					$this->ParentWst = (isset($_POST[$this->Name . '_ParentWst'])) ? 1 : 0;
					$this->ParentWso = (isset($_POST[$this->Name . '_ParentWso'])) ? 1 : 0;
					$this->ParentWsn = (isset($_POST[$this->Name . '_ParentWsn'])) ? 1 : 0;
					$this->ParentWsnl = (isset($_POST[$this->Name . '_ParentWsnl'])) ? 1 : 0;
				}
				break;
			case 1:
				foreach($this->permissions_slots as $pkey => $pval){
					foreach($pval as $k => $v){

						$obj = $this->Name . '_Permission_' . $k;
						$this->setPermission($k, (isset($_POST[$obj]) ? 1 : 0));
					}
				}
				$obj = $this->Name . '_ParentPerms';
				$this->ParentPerms = (isset($_POST[$obj])) ? 1 : 0;
				break;
			case 2:
				foreach($this->workspaces as $k => $v){
					$obj = $this->Name . '_Workspace_' . $k . '_Values';
					if(isset($_POST[$obj])){
						$this->workspaces[$k] = ($_POST[$obj] != '' ? explode(',', $_POST[$obj]) : array());
					}
					$obj = $this->Name . '_defWorkspace_' . $k . '_Values';
					if(isset($_POST[$obj])){
						$this->workspaces_defaults[$k] = ($_POST[$obj] != '' ? explode(',', $_POST[$obj]) : array());
					}
				}
				$this->ParentWs = (isset($_POST[$this->Name . '_ParentWs'])) ? 1 : 0;
				$this->ParentWst = (isset($_POST[$this->Name . '_ParentWst'])) ? 1 : 0;
				$this->ParentWso = (isset($_POST[$this->Name . '_ParentWso'])) ? 1 : 0;
				$this->ParentWsn = (isset($_POST[$this->Name . '_ParentWsn'])) ? 1 : 0;
				$this->ParentWsnl = (isset($_POST[$this->Name . '_ParentWsnl'])) ? 1 : 0;
				break;
			case 3:
				foreach($this->preference_slots as $val){
					if($val == 'seem_start_file' || $val == 'seem_start_type' || $val == 'seem_start_weapp'){
						$obj = '';
					} else{
						$obj = $this->Name . '_Preference_' . $val;
					}
					$this->setPreference($val, (isset($_POST[$obj]) ? $_POST[$obj] : 0));
				}
				switch($_REQUEST['seem_start_type']){
					case 'cockpit':
						$this->setPreference('seem_start_file', 0);
						$this->setPreference('seem_start_type', 'cockpit');
						break;
					case 'object':
						$this->setPreference('seem_start_file', $_REQUEST['seem_start_object']);
						$this->setPreference('seem_start_type', 'object');
						break;
					case 'weapp':
						$this->setPreference('seem_start_weapp', $_REQUEST['seem_start_weapp']);
						$this->setPreference('seem_start_type', 'weapp');
						break;
					default:
						$this->setPreference('seem_start_file', $_REQUEST['seem_start_document']);
						$this->setPreference('seem_start_type', 'document');
				}
				break;
		}
		foreach($this->extensions_slots as $k => $v){
			$this->extensions_slots[$k]->perserve($tab, $sub_tab);
		}
	}

	function checkPermission($perm){
		foreach($this->permissions_slots as $key => $val){
			foreach($val as $key => $val){
				if($key == $perm){
					return ($val ? true : false);
				}
			}
		}
		return false;
	}

	function resetOwnersCreatorModifier(){
		$newID = intval($_SESSION['user']['ID']);
		$this->ID = intval($this->ID);
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET Owners='' WHERE Owners=','");
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET Owners='' WHERE Owners=','");
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET CreatorID='$newID'  WHERE CreatorID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET CreatorID='$newID'  WHERE CreatorID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET ModifierID='$newID'  WHERE ModifierID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET ModifierID='$newID'  WHERE ModifierID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . USER_TABLE . " SET CreatorID='$newID'  WHERE CreatorID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . USER_TABLE . " SET ModifierID='$newID'  WHERE ModifierID=" . $this->ID);

		if(defined('OBJECT_TABLE')){
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET Owners='' WHERE Owners=','");
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET Owners='' WHERE Owners=','");
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET CreatorID='$newID'  WHERE CreatorID=" . $this->ID);
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET CreatorID='$newID'  WHERE CreatorID=" . $this->ID);
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET ModifierID='$newID'  WHERE ModifierID=" . $this->ID);
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET ModifierID='$newID'  WHERE ModifierID=" . $this->ID);
		}
	}

	function deleteMe(){
		foreach($this->extensions_slots as $k => $v){
			$this->extensions_slots[$k]->delete();
		}
		$this->ID = intval($this->ID);
		switch($this->Type){
			case self::TYPE_USER:
				$this->DB_WE->query('DELETE FROM ' . USER_TABLE . ' WHERE ID=' . $this->ID);
				$this->DB_WE->query('DELETE FROM ' . PREFS_TABLE . ' WHERE userID=' . $this->ID);
				$this->resetOwnersCreatorModifier();
				$this->removeAccount();
				return true;
			case self::TYPE_USER_GROUP:
				$this->DB_WE->query('SELECT ID FROM ' . USER_TABLE . ' WHERE ParentID=' . $this->ID);
				while($this->DB_WE->next_record()) {
					$tmpobj = new we_user();
					$tmpobj->initFromDB($this->DB_WE->f('ID'));
					$tmpobj->deleteMe();
				}
				$this->DB_WE->query('DELETE FROM ' . USER_TABLE . ' WHERE ID=' . $this->ID);
				$this->resetOwnersCreatorModifier();
				return true;
			case self::TYPE_ALIAS:
				$this->DB_WE->query('DELETE FROM ' . USER_TABLE . ' WHERE ID=' . $this->ID);
				return true;
		}
		return false;
	}

	function isLastAdmin(){
		$this->ID = intval($this->ID);
		$exist = (f('SELECT 1 AS a FROM ' . USER_TABLE . " WHERE Permissions LIKE ('%\"ADMINISTRATOR\";i:1;%') AND ID!=" . $this->ID, 'a', $this->DB_WE) == '1');
		if($exist){
			return false;
		} else{
			if(($id = intval(f('SELECT ID FROM ' . USER_TABLE . " WHERE Permissions LIKE ('%\"ADMINISTRATOR\";s:1:\"1\";%') AND ID!=" . $this->ID, 'ID', $this->DB_WE)))){
				print $id . we_html_element::htmlBr();
				return false;
			}
		}
		return true;
	}

	function getPath($id = 0){
		$db_tmp = new DB_WE();
		$path = '';
		if($id == 0){
			$id = intval($this->ParentID);
			$path = $db_tmp->escape($this->username);
		}
		$foo = getHash('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($id), $db_tmp);
		$path = '/' . (isset($foo['username']) ? $foo['username'] : '') . $path;
		$pid = isset($foo['ParentID']) ? $foo['ParentID'] : '';
		while($pid > 0) {
			$db_tmp->query('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()) {
				$path = '/' . $db_tmp->f('username') . $path;
				$pid = $db_tmp->f('ParentID');
			}
		}
		return $path;
	}

	function getAllPermissions(){
		$user_permissions = array();
		foreach($this->permissions_slots as $key => $val){
			foreach($val as $k => $v){
				$user_permissions[$k] = $v;
			}
		}
		$db_tmp = new DB_WE;
		$this->DB_WE->query('SELECT ParentID,ParentPerms,Permissions,Alias FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ID) . ' OR Alias=' . intval($this->ID));
		$group_permissions = array();
		while($this->DB_WE->next_record()) {
			if($this->DB_WE->f('Alias') != $this->ID){
				$group_permissions = unserialize($this->DB_WE->f('Permissions'));
				if(is_array($group_permissions)){
					foreach($user_permissions as $key => $val){
						if(isset($group_permissions[$key])){
							$user_permissions[$key] = $user_permissions[$key] | $group_permissions[$key];
						}
					}
				}
			}
			$lpid = $this->DB_WE->f('ParentID');
			if($this->DB_WE->f('ParentPerms')){
				while($lpid) {
					$db_tmp->query('SELECT ParentID,ParentPerms,Permissions FROM ' . USER_TABLE . ' WHERE ID=' . intval($lpid));
					if($db_tmp->next_record()){
						$group_permissions = unserialize($db_tmp->f('Permissions'));
						if(is_array($group_permissions)){
							foreach($user_permissions as $key => $val){
								if(isset($group_permissions[$key])){
									$user_permissions[$key] = $user_permissions[$key] | $group_permissions[$key];
								}
							}
							$lpid = ($db_tmp->f('ParentPerms') ? $db_tmp->f('ParentID') : 0);
						} else{
							$lpid = 0;
						}
					} else{
						$lpid = 0;
					}
				}
			}
		}
		return $user_permissions;
	}

	function getState(){
		//FIXME: use __sleep/__wakeup + serialize/unserialize
		$state = '
$this->Name=' . var_export($this->Name, true) . ';
$this->Table=' . var_export($this->Table, true) . ';
$this->permissions_slots=' . var_export($this->permissions_slots, true) . ';
$this->workspaces=' . var_export($this->workspaces, true) . ';
$this->workspaces_defaults=' . var_export($this->workspaces_defaults, true) . ';
$this->Preferences=' . var_export($this->Preferences, true) . ';
';

		foreach($this->persistent_slots as $k => $v){
			$attrib = isset($this->$v) ? $this->$v : null;
			$state.='$this->' . $v . '=' . var_export($attrib, true) . ';';
		}


		foreach($this->extensions_slots as $k => $v){
			$state.='$this->extensions_slots[\'' . $k . '\']=new ' . $v->ClassName . '();
			$this->extensions_slots[\'' . $k . '\']->init($this);' .
				$this->extensions_slots[$k]->getState('$this->extensions_slots[\'' . $k . '\']');
		}
		return serialize($state);
	}

	function setState($state){
		//FIXME: use __sleep/__wakeup + serialize/unserialize
		$code = unserialize($state);
		eval($code);
	}

	/**
	 * LAYOUT FUNCTIONS
	 */
	function formDefinition($tab, $perm_branch){
		$yuiSuggest = & weSuggest::getInstance();
		switch($tab){
			case 0:
				return $yuiSuggest->getYuiJsFiles() .
					$this->formGeneralData() .
					$yuiSuggest->getYuiCss();
			//.$yuiSuggest->getYuiJs();
			case 1:
				return $this->formPermissions($perm_branch);
			case 2:
				return $yuiSuggest->getYuiJsFiles() .
					$this->formWorkspace() .
					$yuiSuggest->getYuiCss();
			//.$yuiSuggest->getYuiJs();
			case 3:
				return $this->formPreferences($perm_branch);
		}
		foreach($this->extensions_slots as $k => $v){
			return $this->extensions_slots[$k]->formDefinition($tab, $perm_branch);
		}
		return $this->formGeneralData();
	}

	function formGeneralData(){
		switch($this->Type){
			case 0:
				return $this->formUserData();
			case 1:
				return $this->formGroupData();
			case 2:
				return $this->formAliasData();
		}
	}

	function formGroupData(){
		$_attr = array('border' => 0, 'cellpadding' => 2, 'cellspacing' => 0);
		$js = we_button::create_state_changer();

		$_tableObj = new we_html_table($_attr, 5, 1);

		$_username = ($this->ID) ? we_html_tools::htmlFormElementTable('<b class="defaultfont">' . $this->username . '</b><input type="hidden" id="yuiAcInputPathName" value="' . ($this->username) . '">', g_l('modules_users', "[group_name]")) : $this->getUserfield("username", "group_name", "text", "255", false, 'id="yuiAcInputPathName" onblur="parent.frames[0].setPathName(this.value); parent.frames[0].setTitlePath();"');
		$_description = '<textarea name="' . $this->Name . '_Description" cols="25" rows="5" style="width:560px" class="defaultfont" onChange="top.content.setHot();">' . $this->Description . '</textarea>';
		$parent_name = f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->DB_WE);

		$parent_name = ($parent_name ? $parent_name : '/');

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('PathGroup');
		$yuiSuggest->setContentType('folder');
		$yuiSuggest->setInput($this->Name . '_ParentID_Text', $parent_name, array('onChange' => 'top.content.setHot()'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(false);
		$yuiSuggest->setResult($this->Name . '_ParentID', $this->ParentID);
		$yuiSuggest->setSelector('Dirselector');
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(450);
		$yuiSuggest->setSelectButton(we_button::create_button('select', "javascript:we_cmd('browse_users','document.we_form." . $this->Name . "_ParentID.value','document.we_form." . $this->Name . "_ParentID_Text.value','group',document.we_form." . $this->Name . "_ParentID.value);"));

		$weAcSelector = $yuiSuggest->getHTML();

		$_tableObj->setCol(0, 0, null, $_username);
		$_tableObj->setCol(1, 0, null, we_html_tools::getPixel(560, 4));
		$_tableObj->setCol(2, 0, null, we_html_tools::htmlFormElementTable($_description, g_l('modules_users', '[description]')));
		$_tableObj->setCol(3, 0, null, we_html_tools::getPixel(560, 10));
		$_tableObj->setCol(4, 0, null, we_html_tools::htmlFormElementTable($weAcSelector, g_l('modules_users', '[group]')));

		$parts = array(
			array(
				'headline' => g_l('modules_users', '[group_data]'),
				'html' => $_tableObj->getHtml(),
				'space' => 120
			)
		);

		$content = '<select name="' . $this->Name . '_Users" size="8" style="width:560px" onChange="if(this.selectedIndex > -1){edit_enabled = switch_button_state(\'edit\', \'edit_enabled\', \'enabled\');}else{edit_enabled = switch_button_state(\'edit\', \'edit_enabled\', \'disabled\');}" ondblclick="top.content.we_cmd(\'display_user\',document.we_form.' . $this->Name . '_Users.value)">';
		if($this->ID){
			$this->DB_WE->query('SELECT ID,username,Text,Type FROM ' . USER_TABLE . ' WHERE Type IN (0,2) AND ParentID=' . intval($this->ID));
			while($this->DB_WE->next_record()) {
				$content.='<option value="' . $this->DB_WE->f("ID") . '">' . (($this->DB_WE->f("Type") == 2) ? "[" : "") . $this->DB_WE->f("Text") . (($this->DB_WE->f("Type") == 2) ? "]" : "");
			}
		}

		$content.='</select><br/>' . we_html_tools::getPixel(5, 10) . '<br>' . we_button::create_button("edit", "javascript:we_cmd('display_user',document.we_form." . $this->Name . "_Users.value)", true, -1, -1, "", "", true, false);

		$parts[] = array(
			'headline' => g_l('modules_users', '[user]'),
			'html' => $content,
			'space' => 120
		);

		return $js . we_multiIconBox::getHTML('', '100%', $parts, 30);
	}

	function getUserfield($name, $lngkey, $type = 'text', $maxlen = 255, $noNull = false, $attribs = ''){
		$val = $this->$name;
		if($noNull && !$val){
			$val = '';
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->Name . '_' . $name, 20, $val, $maxlen, 'onchange="top.content.setHot()" ' . (empty($attribs) ? '' : $attribs), $type, 240), g_l('modules_users', "[$lngkey]"));
	}

	function formUserData(){

		$_attr = array('border' => '0', 'cellpadding' => '2', 'cellspacing' => '0');
		$_tableObj = new we_html_table($_attr, 10, 2);

		$_tableObj->setCol(0, 0, null, $this->getUserfield('Salutation', 'salutation'));
		$_tableObj->setCol(0, 1, '');
		$_tableObj->setCol(1, 0, null, $this->getUserfield('First', 'first_name'));
		$_tableObj->setCol(1, 1, null, $this->getUserfield('Second', 'second_name'));
		$_tableObj->setCol(2, 0, null, we_html_tools::getPixel(280, 20));
		$_tableObj->setCol(2, 1, null, we_html_tools::getPixel(280, 5));
		$_tableObj->setCol(3, 0, null, $this->getUserfield('Address', 'address'));
		$_tableObj->setCol(3, 1, null, $this->getUserfield('HouseNo', 'houseno'));
		$_tableObj->setCol(4, 0, null, $this->getUserfield('PLZ', 'PLZ', 'text', '16', true));
		$_tableObj->setCol(4, 1, null, $this->getUserfield('City', 'city'));
		$_tableObj->setCol(5, 0, null, $this->getUserfield('State', 'state'));
		$_tableObj->setCol(5, 1, null, $this->getUserfield('Country', 'country'));
		$_tableObj->setCol(6, 0, null, we_html_tools::getPixel(280, 20));
		$_tableObj->setCol(6, 1, null, we_html_tools::getPixel(280, 5));
		$_tableObj->setCol(7, 0, null, $this->getUserfield('Tel_preselection', 'tel_pre'));
		$_tableObj->setCol(7, 1, null, $this->getUserfield('Telephone', 'telephone'));
		$_tableObj->setCol(8, 0, null, $this->getUserfield('Fax_preselection', 'fax_pre'));
		$_tableObj->setCol(8, 1, null, $this->getUserfield('Fax', 'fax'));
		$_tableObj->setCol(9, 0, null, $this->getUserfield('Handy', 'mobile'));
		$_tableObj->setCol(9, 1, null, $this->getUserfield('Email', 'email'));


		$parts = array(
			array(
				'headline' => g_l('modules_users', '[general_data]'),
				'html' => $_tableObj->getHtml(),
				'space' => 120
			)
		);

		$_tableObj = new we_html_table($_attr, 8, 2);

		$_username = /* ($this->ID) ?
			  we_html_tools::htmlFormElementTable('<b class="defaultfont">' . $this->username . '</b>', g_l('modules_users', "[username]")) : */
			$this->getUserfield('username', 'username', 'text', '255', false, 'id="yuiAcInputPathName" onblur="parent.frames[0].setPathName(this.value); parent.frames[0].setTitlePath();"');

		$_password = (isset($_SESSION['user']['ID']) && $_SESSION['user']['ID'] && $_SESSION['user']['ID'] == $this->ID && !we_hasPerm('EDIT_PASSWD') ?
				'****************' :
				'<input type="hidden" name="' . $this->Name . '_clearpasswd" value="' . $this->clearpasswd . '" />' . we_html_tools::htmlTextInput('input_pass', 20, "", "255", 'onchange="top.content.setHot()" autocomplete="off"', 'password', 240));

		$parent_name = f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->DB_WE);
		$parent_name = $parent_name ? $parent_name : '/';

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('PathGroup');
		$yuiSuggest->setContentType('folder');
		$yuiSuggest->setInput($this->Name . '_ParentID_Text', $parent_name, array('onChange' => 'top.content.setHot()'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($this->Name . '_ParentID', $this->ParentID);
		$yuiSuggest->setSelector('Dirselector');
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(403);
		$yuiSuggest->setSelectButton(we_button::create_button('select', "javascript:we_cmd('browse_users','document.we_form." . $this->Name . "_ParentID.value','document.we_form." . $this->Name . "_ParentID_Text.value','group',document.we_form." . $this->Name . "_ParentID.value);"));

		$weAcSelector = $yuiSuggest->getHTML();

		$_tableObj->setCol(0, 0, null, $_username);
		$_tableObj->setCol(0, 1, null, we_html_tools::htmlFormElementTable($_password, g_l('modules_users', '[password]')));
		$_tableObj->setCol(1, 0, null, we_html_tools::getPixel(280, 10));
		$_tableObj->setCol(1, 1, null, we_html_tools::getPixel(280, 5));
		$_tableObj->setCol(2, 0, null, we_forms::checkboxWithHidden($this->LoginDenied, $this->Name . '_LoginDenied', g_l('modules_users', '[login_denied]'), false, "defaultfont", "top.content.setHot();", ($_SESSION["user"]["ID"] == $this->ID || !we_hasPerm("ADMINISTRATOR"))));
		$_tableObj->setCol(2, 1, array("class" => "defaultfont"), g_l('modules_users', "[lastPing]") . ' ' . (($this->Ping) ? date('d.m.Y H:i:s', $this->Ping) : '-'));
		$_tableObj->setCol(3, 0, null, we_html_tools::getPixel(280, 10));
		$_tableObj->setCol(3, 1, null, we_html_tools::getPixel(280, 5));
		$_tableObj->setCol(4, 0, array("colspan" => "2"), we_html_tools::htmlFormElementTable($weAcSelector, g_l('modules_users', "[group]")));
		$_tableObj->setCol(5, 0, null, we_html_tools::getPixel(280, 10));
		$_tableObj->setCol(5, 1, null, we_html_tools::getPixel(280, 5));
		if($this->CreatorID){
			$this->DB_WE->query('SELECT username,first,second FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->CreatorID));
			$CreatorIDtext = ($this->DB_WE->next_record() ?
					$this->DB_WE->f('username') . ' (' . $this->DB_WE->f('first') . ' ' . $this->DB_WE->f('second') . ')' :
					g_l('modules_users', '[lostID]') . $this->CreatorID . g_l('modules_users', '[lostID2]'));
		} else{
			$CreatorIDtext = '-';
		}
		if($this->ModifierID){
			if($this->ModifierID == $this->ID){
				$ModifierIDtext = $this->username . ' (' . $this->First . ' ' . $this->Second . ')';
			} else{
				$this->DB_WE->query('SELECT username,First,Second FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ModifierID));
				$ModifierIDtext = ($this->DB_WE->next_record() ?
						$this->DB_WE->f('username') . ' (' . $this->DB_WE->f('First') . ' ' . $this->DB_WE->f('Second') . ')' :
						g_l('modules_users', '[lostID]') . $this->ModifierID . g_l('modules_users', '[lostID2]'));
			}
		} else{
			$ModifierIDtext = '-';
		}
		$_tableObj->setCol(6, 0, array('class' => 'defaultfont'), g_l('modules_users', '[CreatorID]') . ' ' . $CreatorIDtext);
		$_tableObj->setCol(6, 1, array('class' => 'defaultfont'), g_l('modules_users', '[CreateDate]') . ' ' . (($this->CreateDate) ? date('d.m.Y H:i:s', $this->CreateDate) : '-'));
		$_tableObj->setCol(7, 0, array('class' => 'defaultfont'), g_l('modules_users', '[ModifierID]') . ' ' . $ModifierIDtext);
		$_tableObj->setCol(7, 1, array('class' => 'defaultfont'), g_l('modules_users', '[ModifyDate]') . ' ' . (($this->ModifyDate) ? date('d.m.Y H:i:s', $this->ModifyDate) : '-'));
		$parts[] = array(
			'headline' => g_l('modules_users', '[user_data]'),
			'html' => $_tableObj->getHtml(),
			'space' => 120
		);

		return we_multiIconBox::getHTML('', '100%', $parts, 30);
	}

	/**
	 * This function outputs the group of selectable user permissions
	 *
	 * @param      $branch                                 string
	 *
	 * @return     string
	 */
	function formPermissions($branch){
		global $perm_defaults;

		// Set output text
		// Create a object of the class dynamicControls
		$dynamic_controls = new we_dynamicControls();
		// Now we create the overview of the user rights
		$content = $dynamic_controls->fold_checkbox_groups($this->permissions_slots, $this->permissions_main_titles, $this->permissions_titles, $this->Name, $branch, array('administrator'), true, true, 'we_form', 'perm_branch', true, true);



		$javascript = '
function rebuildCheckboxClicked() {
	toggleRebuildPerm(false);
}

function toggleRebuildPerm(disabledOnly) {';
		if(isset($this->permissions_slots['rebuildpermissions']) && is_array($this->permissions_slots['rebuildpermissions'])){

			foreach($this->permissions_slots['rebuildpermissions'] as $pname => $pvalue){
				if($pname != 'REBUILD'){
					$javascript .= '
					if (document.we_form.' . $this->Name . '_Permission_REBUILD && document.we_form.' . $this->Name . '_Permission_' . $pname . ') {
						if(document.we_form.' . $this->Name . '_Permission_REBUILD.checked) {
							document.we_form.' . $this->Name . '_Permission_' . $pname . '.disabled = false;
							if (!disabledOnly) {
								document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = true;
							}
						} else {
							document.we_form.' . $this->Name . '_Permission_' . $pname . '.disabled = true;
							if (!disabledOnly) {
								document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = false;
							}
						}
					}
					';
				} else{
					$handler = "
					if (document.we_form." . $this->Name . "_Permission_" . $pname . ") {
						document.we_form." . $this->Name . "_Permission_" . $pname . ".onclick = rebuildCheckboxClicked;
					} else {
						document.we_form." . $this->Name . "_Permission_" . $pname . ".onclick = top.content.setHot();
					}
					toggleRebuildPerm(true);";
				}
			}
		}
		$javascript .= '}';
		if(isset($handler)){
			$javascript .= $handler;
		}

		$parts = array(
			array(
				'headline' => '',
				'html' => $content,
				'space' => 0,
				'noline' => 1
			)
		);

		// js to uncheck all permissions
		$uncheckjs = '';
		$checkjs = '';
		foreach($this->permissions_slots as $group){
			foreach($group as $pname => $pvalue){
				if($pname != 'ADMINISTRATOR'){
					$uncheckjs .= 'document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = false;top.content.setHot();';
					$checkjs .= 'document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = true;top.content.setHot();';
				}
			}
		}

		$button_uncheckall = we_button::create_button('uncheckall', 'javascript:' . $uncheckjs);
		$button_checkall = we_button::create_button('checkall', 'javascript:' . $checkjs);
		$parts[] = array(
			'headline' => '',
			'html' => we_button::create_button_table(array($button_uncheckall, $button_checkall)),
			'space' => 0
		);

		// Check if user has right to decide to give administrative rights
		if(is_array($this->permissions_slots['administrator']) && we_hasPerm('ADMINISTRATOR') && $this->Type == self::TYPE_USER){
			foreach($this->permissions_slots['administrator'] as $k => $v){
				$content = '
<table cellpadding="0" cellspacing="0" border="0" width="500">
	<tr><td>' . we_html_tools::getPixel(1, 5) . '</td></tr>
	<tr><td>' . we_forms::checkbox(($v ? $v : "0"), ($v ? true : false), $this->Name . "_Permission_" . $k, $this->permissions_titles["administrator"][$k], false, "defaultfont", ($k == "REBUILD" ? "setRebuidPerms();top.content.setHot();" : "top.content.setHot();")) . '</td></tr>
</table>';
			}
			$parts[] = array(
				'headline' => '',
				'html' => $content,
				'space' => 0
			);
		}
		$parts[] = array(
			'headline' => '',
			'html' => $this->formInherits('_ParentPerms', $this->ParentPerms, g_l('modules_users', '[inherit]')),
			'space' => 0
		);



		return we_multiIconBox::getHTML('', '100%', $parts, 30) . we_html_element::jsElement($javascript);
	}

	function formWorkspace(){

		$parts = array();
		$content = we_html_element::jsElement('
function addElement(elvalues) {
	elvalues.value=(elvalues.value==""?"0":elvalues.value+",0");
	switchPage(2);
}

function setValues(section) {
	switch(section){
	case 2:
		table="' . TEMPLATES_TABLE . '";
		break;' . (defined("OBJECT_TABLE") ? '
	case 3:
		table="' . OBJECT_FILES_TABLE . '";
		break;' : '') . (defined('NEWSLETTER_TABLE') ? '
	case 5:
		table="' . NEWSLETTER_TABLE . '";
		break;' : '') . '
	case 4:
		table="' . NAVIGATION_TABLE . '";
		break;
	default:
		table="' . FILE_TABLE . '";
	}
	eval(\'fillValues(document.we_form.' . $this->Name . '_Workspace_\'+table+\'_Values,"' . $this->Name . '_Workspace_\'+table+\'")\');
}

function fillValues(elvalues,names) {
	var stack=elvalues.value.split(",");
	elcount=stack.length;
	for(i=0;i<elcount;i++) {
		eval("if(document.we_form."+names+"_"+i+") stack[i]=document.we_form."+names+"_"+i+".value");
	}
	elvalues.value=stack.join();
}

function fillDef(elvalues,elvalues2,names,names2) {
	var stack=elvalues2.value.split(",");
	elcount=stack.length;
	for(i=0;i<elcount;i++) {
		if(document.we_form.elements[names+"_"+i]){
			if(document.we_form.elements[names+"_"+i].checked){
				stack[i]=document.we_form.elements[names2+"_"+i].value;
			} else{
				 stack[i]=0;
			}
		}
	}
	elvalues.value=stack.join();
}

function delElement(elvalues,elem) {
	var stack=elvalues.value.split(",");
	var res=new Array();
	var c=-1;

	for(i=0;i<stack.length;i++) {
		if(i!=elem) {
			c++;
			res[c]=stack[i];
		}
	}
	elvalues.value=res.join();
	top.content.setHot();
}');
		$content1 = '';

		foreach($this->workspaces as $k => $v){
			switch($k){
				case TEMPLATES_TABLE:
					if(defined('WK')){
						break 2;
					}
					$title = g_l('modules_users', '[workspace_templates]');
					break;
				case NAVIGATION_TABLE:
					if(defined('WK')){
						break 2;
					}
					$title = g_l('modules_users', '[workspace_navigations]');
					break;
				case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):
					if(defined('WK')){
						break 2;
					}
					$title = g_l('modules_users', '[workspace_newsletter]');
					break;
				case (defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					if(defined('WK')){
						break 2;
					}
					$title = g_l('modules_users', '[workspace_objects]');
					break;
				default:
					$title = g_l('modules_users', '[workspace_documents]');
					break;
			}
			$obj_values = $this->Name . '_Workspace_' . $k . '_Values';
			$obj_names = $this->Name . '_Workspace_' . $k;
			$obj_def_values = $this->Name . '_defWorkspace_' . $k . '_Values';
			$obj_def_names = $this->Name . '_defWorkspace_' . $k;

			switch($k){
				case TEMPLATES_TABLE:
					$content1 .= $this->formInherits('_ParentWst', $this->ParentWst, g_l('modules_users', '[inherit_wst]'));
					break;
				case NAVIGATION_TABLE:
					$content1 .= $this->formInherits('_ParentWsn', $this->ParentWsn, g_l('modules_users', '[inherit_wsn]'));
					break;
				case (defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$content1 .= $this->formInherits('_ParentWso', $this->ParentWso, g_l('modules_users', '[inherit_wso]'));
					break;
				case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):
					$content1 .= $this->formInherits('_ParentWsnl', $this->ParentWsnl, g_l('modules_users', '[inherit_wsnl]'));
					break;
				default:
					$content1 .= $this->formInherits('_ParentWs', $this->ParentWs, g_l('modules_users', '[inherit_ws]'));
					break;
			}
			$content .= '<p>';

			$content1.='
				<input type="hidden" name="' . $obj_values . '" value="' . implode(",", $v) . '" />
				<input type="hidden" name="' . $obj_def_values . '" value="' . implode(",", $this->workspaces_defaults[$k]) . '" />
				<table border="0" cellpadding="0" cellspacing="2" width="520">';
			foreach($v as $key => $val){
				$value = $val;
				$path = f('SELECT Path FROM ' . $k . ' WHERE ' . $k . '.ID=' . $value, 'Path', $this->DB_WE);
				if(!$path){
					$foo = get_def_ws($k);
					$fooA = makeArrayFromCSV($foo);
					if(count($fooA)){
						$value = $fooA[0];
						$path = id_to_path($value);
					} else{
						$path = '/';
						$value = 0;
					}
				}
				$default = false;
				foreach($this->workspaces_defaults[$k] as $k1 => $v1){
					if($v1 == $val && $v1 != 0){
						$default = true;
					}
				}

				switch($k){
					case TEMPLATES_TABLE:
						$setValue = 2;
						break;
					case (defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
						$setValue = 3;
						break;
					case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):
						$setValue = 5;
						break;
					case NAVIGATION_TABLE:
						$setValue = 4;
						break;
					default:
						$setValue = 1;
				}

				switch($k){
					case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):
						$button = we_button::create_button('select', "javascript:we_cmd('openNewsletterDirselector',document.forms[0]." . $obj_names . "_" . $key . ".value,'document.we_form." . $obj_names . "_" . $key . ".value','document.we_form." . $obj_names . "_" . $key . "_Text.value','opener.top.content.user_resize.user_right.user_editor.user_properties.setValues(" . $setValue . ")','" . session_id() . "','" . (isset($_REQUEST["rootDirID"]) ? $_REQUEST["rootDirID"] : "") . "' )");
						break;
					case NAVIGATION_TABLE:
						$button = we_button::create_button('select', "javascript:we_cmd('openNavigationDirselector',document.forms[0]." . $obj_names . "_" . $key . ".value,'document.we_form." . $obj_names . "_" . $key . ".value','document.we_form." . $obj_names . "_" . $key . "_Text.value','opener.top.content.user_resize.user_right.user_editor.user_properties.setValues(" . $setValue . ")','" . session_id() . "','" . (isset($_REQUEST["rootDirID"]) ? $_REQUEST["rootDirID"] : "") . "' )");
						break;
					default:
						$button = we_button::create_button('select', "javascript:we_cmd('openDirselector',document.forms[0]." . $obj_names . "_" . $key . ".value,'" . $k . "','document.we_form." . $obj_names . "_" . $key . ".value','document.we_form." . $obj_names . "_" . $key . "_Text.value','opener.top.content.user_resize.user_right.user_editor.user_properties.setValues(" . $setValue . ")','" . session_id() . "','" . (isset($_REQUEST["rootDirID"]) ? $_REQUEST["rootDirID"] : "") . "' )");
				}

				$yuiSuggest = & weSuggest::getInstance();
				$yuiSuggest->setAcId('WS' . $k . $key);
				$yuiSuggest->setContentType('folder');
				$yuiSuggest->setInput($obj_names . '_' . $key . '_Text', $path);
				$yuiSuggest->setMaxResults(10);
				$yuiSuggest->setMayBeEmpty(true);
				$yuiSuggest->setResult($obj_names . '_' . $key, $value);
				$yuiSuggest->setSelector('Dirselector');
				$yuiSuggest->setTable($k);
				$yuiSuggest->setWidth(290);
				$yuiSuggest->setSelectButton($button, 10);
				$yuiSuggest->setDoOnTextfieldBlur('setValues(' . $setValue . ');');

				$weAcSelector = $yuiSuggest->getHTML();

				$content1.='
<tr><td colspan="2">' . $weAcSelector . '</td>
	<td><div style="position:relative; top:-1px">' . we_button::create_button("image:btn_function_trash", "javascript:fillValues(document.we_form." . $obj_values . ",'" . $obj_names . "');fillDef(document.we_form." . $obj_def_values . ",document.we_form." . $obj_values . ",'" . $obj_def_names . "','" . $obj_names . "');delElement(document.we_form." . $obj_values . "," . $key . ");delElement(document.we_form." . $obj_def_values . "," . $key . ");switchPage(2);", true) . '</td></div>' .
					($k == FILE_TABLE ?
						'<td class="defaultfont">' . we_forms::checkbox("1", $default, $obj_def_names . "_$key", g_l('modules_users', "[make_def_ws]"), true, "defaultfont", 'top.content.setHot();fillDef(document.we_form.' . $obj_def_values . ',document.we_form.' . $obj_values . ',\'' . $obj_def_names . '\',\'' . $obj_names . '\');') . '</td>' :
						'<td>' . we_html_tools::getPixel(5, 5) . '</td>') . '
</tr>';
			}
			$content1.= '
	<tr>
		<td>' . we_html_tools::getPixel(300, 3) . '</td>
		<td>' . we_html_tools::getPixel(110, 3) . '</td>
		<td>' . we_html_tools::getPixel(40, 3) . '</td>
		<td>' . we_html_tools::getPixel(90, 3) . '</td>
	</tr>
	<tr><td colspan="4">' . we_button::create_button("image:btn_function_plus", "javascript:top.content.setHot();fillValues(document.we_form." . $obj_values . ",'" . $obj_names . "');fillDef(document.we_form." . $obj_def_values . ",document.we_form." . $obj_values . ",'" . $obj_def_names . "','" . $obj_names . "');addElement(document.we_form." . $obj_values . ");addElement(document.we_form." . $obj_def_values . ");", true) . '</td></tr>
</table>';
			$parts[] = array(
				'headline' => $title,
				'html' => $content1,
				'space' => 200
			);

			$content1 = '';
		}

		return $content . we_multiIconBox::getHTML('', '100%', $parts, 30);
	}

	function formPreferences($branch = ''){

		$dynamic_controls = new we_dynamicControls();

		$groups = array(
			'glossary' => g_l('prefs', '[tab_glossary]'),
			'ui' => g_l('prefs', '[tab][ui]'),
			'editor' => g_l('prefs', '[tab][editor]'),
		);

		$titles = $groups;

		$multiboxes = array(
			'glossary' => $this->formPreferencesGlossary(),
			'ui' => $this->formPreferencesUI(),
			'editor' => $this->formPreferencesEditor(),
		);


		$parts = array(
			array(
				'headline' => '',
				'html' => $dynamic_controls->fold_multibox_groups($groups, $titles, $multiboxes, $branch),
				'space' => 0
			)
		);

		return we_multiIconBox::getHTML('', '100%', $parts, 30);
	}

	function formPreferencesGlossary(){
		$_settings = array();

		// Create checkboxes
		$_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 3, 1);

		$_table->setCol(0, 0, null, we_forms::checkbox(1, $this->Preferences['force_glossary_check'], $this->Name . '_Preference_force_glossary_check', g_l('prefs', '[force_glossary_check]'), 'false', 'defaultfont', "top.content.setHot()"));
		$_table->setCol(1, 0, null, we_html_tools::getPixel(1, 5));
		$_table->setCol(2, 0, null, we_forms::checkbox(1, $this->Preferences['force_glossary_action'], $this->Name . "_Preference_force_glossary_action", g_l('prefs', '[force_glossary_action]'), "false", "defaultfont", "top.content.setHot()"));

		// Build dialog if user has permission
		if(we_hasPerm('ADMINISTRATOR')){
			$_settings[] = array('headline' => g_l('prefs', '[glossary_publishing]'), 'html' => $_table->getHtml(), 'space' => 200, 'noline' => 1);
		}

		return $_settings;
	}

	function formPreferencesUI(){
		$_settings = array();


		/*		 * ***************************************************************
		 * LANGUAGE
		 * *************************************************************** */

		//	Look which languages are installed ...
		$_language_directory = dir(WE_INCLUDES_PATH . 'we_language');

		while(false !== ($entry = $_language_directory->read())) {
			if($entry != '.' && $entry != '..'){
				if(is_dir(WE_INCLUDES_PATH . 'we_language/' . $entry)){
					$_language[$entry] = $entry;
				}
			}
		}
		global $_languages;

		if(count($_language)){ // Build language select box
			$_languages = new we_html_select(array('name' => $this->Name . '_Preference_Language', 'class' => 'weSelect'));
			$myCompLang = (isset($this->Preferences['Language']) && $this->Preferences['Language'] != '' ? $this->Preferences['Language'] : $GLOBALS['WE_LANGUAGE']);

			foreach($_language as $key => $value){
				$_languages->addOption($key, $value);

				// Set selected extension
				if($key == $myCompLang){
					$_languages->selectOption($key);
				} else{
					// do nothing
				}
			}


			// Build dialog
			$_settings[] = array('headline' => g_l('prefs', '[choose_language]'), 'html' => $_languages->getHtml(), 'space' => 200, 'noline' => 1);
		}


		$_charset = new we_html_select(array('name' => $this->Name . '_Preference_BackendCharset', 'class' => 'weSelect', 'onChange' => 'top.content.setHot();'));
		$c = charsetHandler::getAvailCharsets();
		foreach($c as $char){
			$_charset->addOption($char, $char);
		}
		$myCompChar = (isset($this->Preferences['BackendCharset']) && $this->Preferences['BackendCharset'] != '' ? $this->Preferences['BackendCharset'] : $GLOBALS['WE_BACKENDCHARSET']);
		$_charset->selectOption($myCompChar);
		$_settings[] = array(
			'headline' => g_l('prefs', '[choose_backendcharset]'),
			'html' => $_charset->getHtml(),
			'space' => 200
		);


		/*		 * ***************************************************************
		 * AMOUNT Number of Columns
		 * *************************************************************** */

		$_amount = new we_html_select(array('name' => $this->Name . '_Preference_cockpit_amount_columns', 'class' => 'weSelect', 'onChange' => "top.content.setHot();"));
		if($this->Preferences['cockpit_amount_columns'] == ''){
			$this->Preferences['cockpit_amount_columns'] = 3;
		}
		for($i = 1; $i <= 10; $i++){
			$_amount->addOption($i, $i);
			if($i == $this->Preferences['cockpit_amount_columns']){
				$_amount->selectOption($i);
			}
		}

		$_settings[] = array(
			'headline' => g_l('prefs', '[cockpit_amount_columns]'),
			'html' => $_amount->getHtml(),
			'space' => 200
		);


		/*		 * ***************************************************************
		 * SEEM
		 * *************************************************************** */

		$_document_path = '';
		// Generate needed JS
		$js = we_html_element::jsElement("
						function select_seem_start() {
							myWind = false;

							for(k=top.opener.top.jsWindow_count;k>-1;k--){

								eval(\"if(top.opener.top.jsWindow\" + k + \"Object){\" +
									 \"	if(top.opener.top.jsWindow\" + k + \"Object.ref == 'edit_module'){\" +
									 \"		myWind = top.opener.top.jsWindow\" + k + \"Object.wind.content.user_resize.user_right.user_editor.user_properties;\" +
									 \"		myWindStr = 'top.jsWindow\" + k + \"Object.wind.content.user_resize.user_right.user_editor.user_properties';\" +
									 \"	}\" +
									 \"}\");
								if(myWind){
									break;
								}
							}

							if(document.getElementById('seem_start_type').value == 'object') {
								top.opener.top.we_cmd('openDocselector', document.forms[0].elements['seem_start_object'].value, '" . (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : "") . "', myWindStr + '.document.forms[0].elements[\'seem_start_object\'].value', myWindStr + '.document.forms[0].elements[\'seem_start_object_name\'].value', '', '" . session_id() . "', '', 'objectFile','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");
							} else {
								top.opener.top.we_cmd('openDocselector', document.forms[0].elements['seem_start_document'].value, '" . FILE_TABLE . "', myWindStr + '.document.forms[0].elements[\'seem_start_document\'].value', myWindStr + '.document.forms[0].elements[\'seem_start_document_name\'].value', '', '" . session_id() . "', '', 'text/webedition','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");
							}
						}

						function show_seem_chooser(val) {
							if(val == 'document') {
								if(document.getElementById('seem_start_object')) {
									document.getElementById('seem_start_object').style.display = 'none';
								}
								document.getElementById('seem_start_document').style.display = 'block';
								document.getElementById('seem_start_weapp').style.display = 'none';
				" . (defined('OBJECT_FILES_TABLE') ? "
							} else if(val == 'object') {
								document.getElementById('seem_start_document').style.display = 'none';
								document.getElementById('seem_start_weapp').style.display = 'none';
								document.getElementById('seem_start_object').style.display = 'block';
						" : '') . "
							} else if(val == 'weapp'){
								document.getElementById('seem_start_document').style.display = 'none';
								document.getElementById('seem_start_object').style.display = 'none';
								document.getElementById('seem_start_weapp').style.display = 'block';

							} else {
								document.getElementById('seem_start_document').style.display = 'none';
								document.getElementById('seem_start_weapp').style.display = 'none';
								if(document.getElementById('seem_start_object')) {
									document.getElementById('seem_start_object').style.display = 'none';
								}

							}
						}");

		// Cockpit
		$_object_path = '';
		$_object_id = 0;
		$_document_path = '';
		$_document_id = 0;

		switch($this->Preferences['seem_start_type']){
			default:
				$_seem_start_type = '0';
				break;
			case 'cockpit':
				$_SESSION['prefs']['seem_start_file'] = 0;
				$_seem_start_type = 'cockpit';
				break;
			case 'object':
				$_seem_start_type = 'object';
				if($this->Preferences['seem_start_file'] != 0){
					$_object_id = $this->Preferences['seem_start_file'];
					$_get_object_paths = getPathsFromTable(OBJECT_FILES_TABLE, '', FILE_ONLY, $_object_id);

					if(isset($_get_object_paths[$_object_id])){ //	seeMode start file exists
						$_object_path = $_get_object_paths[$_object_id];
					}
				}
				break;
			case 'weapp':
				$_seem_start_type = 'weapp';
				if($this->Preferences['seem_start_file'] != 0){

				}
				break;
			// Document
			case 'document':
				$_seem_start_type = 'document';
				if($this->Preferences['seem_start_file'] != 0){
					$_document_id = $this->Preferences['seem_start_file'];
					$_get_document_paths = getPathsFromTable(FILE_TABLE, '', FILE_ONLY, $_document_id);

					if(isset($_get_document_paths[$_document_id])){ //	seeMode start file exists
						$_document_path = $_get_document_paths[$_document_id];
					}
				}
		}

		$_start_type = new we_html_select(array('name' => 'seem_start_type', 'class' => 'weSelect', 'id' => 'seem_start_type', 'onchange' => "show_seem_chooser(this.value); top.content.setHot();"));
		$_start_type->addOption('0', '-');
		$_start_type->addOption('cockpit', g_l('prefs', '[seem_start_type_cockpit]'));
		$_start_type->addOption('document', g_l('prefs', '[seem_start_type_document]'));
		if(defined('OBJECT_FILES_TABLE')){
			$_start_type->addOption('object', g_l('prefs', '[seem_start_type_object]'));
		}


		//weapp
		$_start_weapp = new we_html_select(array('name' => 'seem_start_weapp', 'class' => 'weSelect', 'id' => 'seem_start_weapp', 'onchange' => 'top.content.setHot();'));
		$_tools = weToolLookup::getAllTools(true, false);
		foreach($_tools as $_tool){
			if(!$_tool['appdisabled'] && isset($this->permissions_slots[$_tool['name']][$_tool['startpermission']]) && $this->permissions_slots[$_tool['name']][$_tool['startpermission']]){
				$_start_weapp->addOption($_tool['name'], $_tool['text']);
			}
		}

		if($_start_weapp->getOptionNum()){
			$_start_type->addOption('weapp', g_l('prefs', '[seem_start_type_weapp]'));
		}

		$_start_type->selectOption($_seem_start_type);
		$_start_weapp->selectOption($this->Preferences['seem_start_weapp']);
		$weAPPSelector = $_start_weapp->getHtml();

		$_seem_weapp_chooser = we_button::create_button_table(array($weAPPSelector), 10, array('id' => 'seem_start_weapp', 'style' => 'display:none'));


		// Build SEEM select start document chooser
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('Doc');
		$yuiSuggest->setContentType('folder,text/webedition,image/*,text/js,text/css,text/html,application/*,video/quicktime');
		$yuiSuggest->setInput('seem_start_document_name', $_document_path);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('seem_start_document', $_document_id);
		$yuiSuggest->setSelector('Docselector');
		$yuiSuggest->setWidth(191);
		$yuiSuggest->setSelectButton(we_button::create_button('select', 'javascript:select_seem_start()', true, 100, 22, '', '', false, false), 10);
		$yuiSuggest->setContainerWidth(299);

		$weAcSelector = $yuiSuggest->getHTML();

		$_seem_document_chooser = we_button::create_button_table(array($weAcSelector), 0, array('id' => 'seem_start_document', 'style' => 'display:none'));

		// Build SEEM select start object chooser
		$yuiSuggest->setAcId('Obj');
		$yuiSuggest->setContentType('folder,objectFile');
		$yuiSuggest->setInput('seem_start_object_name', $_object_path);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('seem_start_object', $_object_id);
		$yuiSuggest->setSelector('Docselector');
		if(defined('OBJECT_FILES_TABLE')){
			$yuiSuggest->setTable(OBJECT_FILES_TABLE);
		}
		$yuiSuggest->setWidth(191);
		$yuiSuggest->setSelectButton(we_button::create_button('select', 'javascript:select_seem_start()', true, 100, 22, '', '', false, false), 10);
		$yuiSuggest->setContainerWidth(299);

		$weAcSelector = $yuiSuggest->getHTML();

		$_seem_object_chooser = we_button::create_button_table(array($weAcSelector), 10, array('id' => 'seem_start_object', 'style' => 'display:none'));

		// Build final HTML code
		$_seem_html = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 2, 1);
		$_seem_html->setCol(0, 0, array('class' => 'defaultfont'), $_start_type->getHtml() . we_html_tools::getPixel(200, 1));
		$_seem_html->setCol(1, 0, null, $_seem_document_chooser . $_seem_object_chooser . $_seem_weapp_chooser);

		if(we_hasPerm('CHANGE_START_DOCUMENT')){
			$_settings[] = array(
				'headline' => g_l('prefs', '[seem_startdocument]'),
				'html' => $js . $_seem_html->getHtml() . we_html_element::jsElement('show_seem_chooser("' . $_seem_start_type . '");'),
				'space' => 200
			);
		}

		/*		 * ***************************************************************
		 * TREE
		 * *************************************************************** */

		$_value_selected = false;
		$_tree_count = $this->Preferences['default_tree_count'];

		$_file_tree_count = new we_html_select(array('name' => $this->Name . '_Preference_default_tree_count', 'class' => 'weSelect', 'onChange' => 'top.content.setHot();'));

		$_file_tree_count->addOption(0, g_l('prefs', '[all]'));
		if(0 == $_tree_count){
			$_file_tree_count->selectOption(0);
			$_value_selected = true;
		}

		for($i = 10; $i < 51; $i+=10){
			$_file_tree_count->addOption($i, $i);

			// Set selected extension
			if($i == $_tree_count){
				$_file_tree_count->selectOption($i);
				$_value_selected = true;
			}
		}

		for($i = 100; $i < 501; $i+=100){
			$_file_tree_count->addOption($i, $i);

			// Set selected extension
			if($i == $_tree_count){
				$_file_tree_count->selectOption($i);
				$_value_selected = true;
			}
		}

		if(!$_value_selected){
			$_file_tree_count->addOption($_tree_count, $_tree_count);
			// Set selected extension
			$_file_tree_count->selectOption($_tree_count);
		}

		$_settings[] = array('headline' => g_l('prefs', '[tree_title]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[tree_count_description]'), 2) . '<br>' . $_file_tree_count->getHtml(), 'space' => 200);


		/*		 * ***************************************************************
		 * WINDOW DIMENSIONS
		 * *************************************************************** */

		$_window_max = false;
		$_window_specify = false;

		if($this->Preferences['sizeOpt'] == 0){
			$_window_max = true;
		} elseif($this->Preferences['sizeOpt'] == 1){
			$_window_specify = true;
		}

		// Build maximize window
		$_window_max_code = we_forms::radiobutton(0, $this->Preferences['sizeOpt'] == 0, $this->Name . '_Preference_sizeOpt', g_l('prefs', '[maximize]'), true, 'defaultfont', "document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = true;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = true;top.content.setHot();");

		// Build specify window dimension
		$_window_specify_code = we_forms::radiobutton(1, !($this->Preferences['sizeOpt'] == 0), $this->Name . '_Preference_sizeOpt', g_l('prefs', '[specify]'), true, 'defaultfont', "document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;top.content.setHot();");

		// Create specify window dimension input
		$_window_specify_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 4, 4);

		$_window_specify_table->setCol(0, 0, null, we_html_tools::getPixel(1, 10));
		$_window_specify_table->setCol(1, 0, null, we_html_tools::getPixel(40, 1));
		$_window_specify_table->setCol(2, 0, null, we_html_tools::getPixel(1, 5));
		$_window_specify_table->setCol(3, 0, null, we_html_tools::getPixel(40, 1));

		$_window_specify_table->setCol(1, 1, array('class' => 'defaultfont'), g_l('prefs', '[width]') . ':');
		$_window_specify_table->setCol(3, 1, array('class' => 'defaultfont'), g_l('prefs', '[height]') . ':');

		$_window_specify_table->setCol(1, 2, null, we_html_tools::getPixel(10, 1));
		$_window_specify_table->setCol(3, 2, null, we_html_tools::getPixel(10, 1));

		$_window_specify_table->setCol(1, 3, null, we_html_tools::htmlTextInput($this->Name . '_Preference_weWidth', 6, ($this->Preferences['weWidth'] != '' && $this->Preferences['weWidth'] != '0' ? $this->Preferences['weWidth'] : 800), 4, ($this->Preferences['sizeOpt'] == 0 ? "disabled=\"disabled\"" : "") . "onChange='top.content.setHot();'", "text", 60));
		$_window_specify_table->setCol(3, 3, null, we_html_tools::htmlTextInput($this->Name . "_Preference_weHeight", 6, ( ($this->Preferences['weHeight'] != '' && $this->Preferences['weHeight'] != '0') ? $this->Preferences['weHeight'] : 600), 4, ($this->Preferences['sizeOpt'] == 0 ? "disabled=\"disabled\"" : "") . "onChange='top.content.setHot();'", "text", 60));

		// Build apply current window dimension
		$_window_current_dimension_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 2);

		$_window_current_dimension_table->setCol(0, 0, null, we_html_tools::getPixel(90, 1));
		$_window_current_dimension_table->setCol(0, 1, null, we_button::create_button("apply_current_dimension", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = " . (we_base_browserDetect::isIE() ? "top.opener.top.document.body.clientWidth" : "top.opener.top.window.outerWidth") . ";document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = " . (we_base_browserDetect::isIE() ? "top.opener.top.document.body.clientHeight;" : "top.opener.top.window.outerHeight;"), true, 210));

		// Build final HTML code
		$_window_html = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 5, 1);
		$_window_html->setCol(0, 0, null, $_window_max_code);
		$_window_html->setCol(1, 0, null, we_html_tools::getPixel(1, 10));
		$_window_html->setCol(2, 0, null, $_window_specify_code . $_window_specify_table->getHtml());
		$_window_html->setCol(3, 0, null, we_html_tools::getPixel(1, 10));
		$_window_html->setCol(4, 0, null, $_window_current_dimension_table->getHtml());

		// Build dialog
		$_settings[] = array("headline" => g_l('prefs', '[dimension]'), "html" => $_window_html->getHtml(), "space" => 200);

		// Create predefined window dimension buttons
		$_window_predefined_table = new we_html_table(array("border" => "0", "align" => "right", "cellpadding" => "1", "cellspacing" => "0"), 3, 1);

		$_window_predefined_table->setCol(0, 0, null, we_button::create_button_table(array(we_button::create_button("res_800", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '800';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '600';", true), we_button::create_button("res_1024", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '1024';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '768';", true))));
		$_window_predefined_table->setCol(2, 0, null, we_button::create_button_table(array(we_button::create_button("res_1280", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '1280';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '960';", true), we_button::create_button("res_1600", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '1600';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '1200';", true))));

		$_window_predefined_table->setCol(1, 0, null, we_html_tools::getPixel(1, 10));

		// Build dialog
		$_settings[] = array("headline" => g_l('prefs', '[predefined]'), "html" => $_window_predefined_table->getHtml(), "space" => 200);

		$_settings_cookie = weGetCookieVariable("but_settings_predefined");

		/**
		 * BUILD FINAL DIALOG
		 */
		return $_settings;
	}

	function formPreferencesEditor(){

		//Editor Mode
		$_template_editor_mode = new we_html_select(array("class" => "weSelect", "name" => $this->Name . "_Preference_editorMode", "size" => "1", "onchange" => "displayEditorOptions(this.options[this.options.selectedIndex].value);"));
		$_template_editor_mode->addOption('textarea', g_l('prefs', '[editor_plaintext]'));
		$_template_editor_mode->addOption('codemirror2', g_l('prefs', '[editor_javascript2]'));
		$_template_editor_mode->addOption('java', g_l('prefs', '[editor_java]'));
		$_template_editor_mode->selectOption($this->Preferences['editorMode']);
		$_settings = array(
			array("headline" => g_l('prefs', '[editor_mode]'), "html" => $_template_editor_mode->getHtml(), "space" => 150)
		);

		$_template_fonts = array('Arial', 'Courier', 'Courier New', 'Helvetica', 'Monaco', 'Mono', 'Tahoma', 'Verdana', 'serif', 'sans-serif', 'none');
		$_template_font_sizes = array(8, 9, 10, 11, 12, 14, 16, 18, 24, 32, 48, 72, -1);

		$_template_editor_font_specify = false;
		$_template_editor_font_size_specify = false;

		if($this->Preferences['editorFontname'] != "" && $this->Preferences['editorFontname'] != "none"){
			$_template_editor_font_specify = true;
		}

		if($this->Preferences['editorFontsize'] != "" && $this->Preferences['editorFontsize'] != -1){
			$_template_editor_font_size_specify = true;
		}

		// Build specify font
		$_template_editor_font_specify_code = we_forms::checkbox(1, $_template_editor_font_specify, $this->Name . "_Preference_editorFont", g_l('prefs', '[specify]'), true, "defaultfont", "top.content.setHot(); if (document.getElementsByName('" . $this->Name . "_Preference_editorFont')[0].checked) { document.getElementsByName('" . $this->Name . "_Preference_editorFontname')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_editorFontsize')[0].disabled = false; } else { document.getElementsByName('" . $this->Name . "_Preference_editorFontname')[0].disabled = true;document.getElementsByName('" . $this->Name . "_Preference_editorFontsize')[0].disabled = true; }");

		// Create specify window dimension input
		$_template_editor_font_specify_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 4, 4);

		$_template_editor_font_specify_table->setCol(0, 0, null, we_html_tools::getPixel(1, 10));
		$_template_editor_font_specify_table->setCol(1, 0, null, we_html_tools::getPixel(50, 1));
		$_template_editor_font_specify_table->setCol(2, 0, null, we_html_tools::getPixel(1, 5));
		$_template_editor_font_specify_table->setCol(3, 0, null, we_html_tools::getPixel(50, 1));

		$_template_editor_font_specify_table->setCol(1, 1, array("class" => "defaultfont"), g_l('prefs', '[editor_fontname]') . ":");
		$_template_editor_font_specify_table->setCol(3, 1, array("class" => "defaultfont"), g_l('prefs', '[editor_fontsize]') . ":");

		$_template_editor_font_specify_table->setCol(1, 2, null, we_html_tools::getPixel(10, 1));
		$_template_editor_font_specify_table->setCol(3, 2, null, we_html_tools::getPixel(10, 1));

		$_template_editor_font_select_box = new we_html_select(array("class" => "weSelect", "name" => $this->Name . "_Preference_editorFontname", "size" => "1", "style" => "width: 90px;", ($_template_editor_font_specify ? "enabled" : "disabled") => ($_template_editor_font_specify ? "enabled" : "disabled"), "onChange" => "top.content.setHot();"));

		foreach($_template_fonts as $tf){
			$_template_editor_font_select_box->addOption($tf, $tf);

			if(!$_template_editor_font_specify){
				if($tf == "Courier New"){
					$_template_editor_font_select_box->selectOption($tf);
				}
			} else{
				if($tf == $this->Preferences['editorFontname']){
					$_template_editor_font_select_box->selectOption($tf);
				}
			}
		}

		$_template_editor_font_sizes_select_box = new we_html_select(array("class" => "weSelect", "name" => $this->Name . "_Preference_editorFontsize", "size" => "1", "style" => "width: 90px;", ($_template_editor_font_size_specify ? "enabled" : "disabled") => ($_template_editor_font_size_specify ? "enabled" : "disabled"), "onChange" => "top.content.setHot();"));

		foreach($_template_font_sizes as $tf){
			$_template_editor_font_sizes_select_box->addOption($tf, $tf);

			if(!$_template_editor_font_specify){
				if($tf == 11){
					$_template_editor_font_sizes_select_box->selectOption($tf);
				}
			} else{
				if($tf == $this->Preferences['editorFontsize']){
					$_template_editor_font_sizes_select_box->selectOption($tf);
				}
			}
		}

		$_template_editor_font_specify_table->setCol(1, 3, null, $_template_editor_font_select_box->getHtml());
		$_template_editor_font_specify_table->setCol(3, 3, null, $_template_editor_font_sizes_select_box->getHtml());

		// Build dialog
		$_settings[] = array("headline" => g_l('prefs', '[editor_font]'), "html" => $_template_editor_font_specify_code . $_template_editor_font_specify_table->getHtml(), "space" => 200);

		$_settings_cookie = weGetCookieVariable("but_settings_editor_predefined");

		return $_settings;
	}

	function formAliasData(){
		$alias_text = ($this->ID ? f("SELECT Path FROM " . USER_TABLE . " WHERE ID=" . intval($this->Alias), 'Path', $this->DB_WE) : '');
		$parent_text = ($this->ParentID == 0 ? '/' : f("SELECT Path FROM " . USER_TABLE . " WHERE ID=" . intval($this->ParentID), 'Path', $this->DB_WE));

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("PathName");
		$yuiSuggest->setContentType("0,1"); // in USER_TABLE is Type 0 folder, Type 1 user and Type 2 alias. Field ContentType is not setted so in weSelectorQuery is a workaroun for USER_TABLE
		$yuiSuggest->setInput($this->Name . '_Alias_Text', $alias_text, array("onChange" => "top.content.setHot();"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(false);
		$yuiSuggest->setResult($this->Name . '_Alias', $this->Alias);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(200);
		$yuiSuggest->setSelectButton(we_button::create_button("select", "javascript:we_cmd('browse_users','document.we_form." . $this->Name . "_Alias.value','document.we_form." . $this->Name . "_Alias_Text.value','noalias',document.we_form." . $this->Name . "_Alias.value)"));

		$weAcSelectorName = $yuiSuggest->getHTML();

		$yuiSuggest->setAcId("PathGroup");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($this->Name . '_ParentID_Text', $parent_text, array("onChange" => "top.content.setHot();"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($this->Name . '_ParentID', $this->ParentID);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(200);
		$yuiSuggest->setSelectButton(we_button::create_button("select", "javascript:we_cmd('browse_users','document.we_form." . $this->Name . "_ParentID.value','document.we_form." . $this->Name . "_ParentID_Text.value','group',document.we_form." . $this->Name . "_ParentID.value)"));

		$weAcSelectorGroup = $yuiSuggest->getHTML();

		$content = '
			<table cellpadding="0" cellspacing="0" border="0" width="530">
			<colgroup><col style="width:170px;"/><col style="width:330px;"/></colgroup>
				<tr>
					<td class="defaultfont">' . g_l('modules_users', "[user]") . ':</td>
					<td>' . $weAcSelectorName . '</td>
				</tr>
				<tr>
					<td colspan="2" style="height:5px;"></td>
				</tr>
				<tr>
					<td class="defaultfont">' . g_l('modules_users', "[group_member]") . ':</td>
					<td>' . $weAcSelectorGroup . '</td>
				</tr>
				<tr>
					<td colspan="2" style="height:1px;"></td>
				</tr>
			</table>';

		$parts = array(
			array(
				"headline" => g_l('modules_users', "[alias_data]"),
				"html" => $content,
				"space" => 120
			)
		);

		$content = $this->formInherits("_ParentPerms", $this->ParentPerms, g_l('modules_users', "[inherit]")) . we_html_tools::getPixel(5, 5) .
			$this->formInherits("_ParentWs", $this->ParentWs, g_l('modules_users', "[inherit_ws]")) . we_html_tools::getPixel(5, 5) .
			$this->formInherits("_ParentWst", $this->ParentWst, g_l('modules_users', "[inherit_wst]"));


		$parts[] = array(
			"headline" => g_l('modules_users', "[rights_and_workspaces]"),
			"html" => $content,
			"space" => 120
		);

		return we_multiIconBox::getHTML('', '100%', $parts, 30);
	}

	function formInherits($name, $value, $title){
		$content = '
			<table cellpadding="0" cellspacing="0" border="0" width="500">
				<tr>
					<td class="defaultfont">' .
			we_forms::checkbox(1, ($value ? true : false), $this->Name . $name, $title, "", "defaultfont", "top.content.setHot();") . '

				</tr>
			</table>';
		return $content;
	}

	function formHeader($tab = 0){
		$we_tabs = new we_tabs();

		if($this->Type == self::TYPE_ALIAS){
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][data]'), 'TAB_ACTIVE', 'setTab(0);'));
		} else{
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][data]'), ($tab == 0 ? 'TAB_ACTIVE' : 'TAB_NORMAL'), 'self.setTab(0);'));

			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][permissions]'), ($tab == 1 ? 'TAB_ACTIVE' : 'TAB_NORMAL'), 'self.setTab(1);'));
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][workspace]'), ($tab == 2 ? 'TAB_ACTIVE' : 'TAB_NORMAL'), 'self.setTab(2);'));

			if($this->Type == self::TYPE_USER){
				$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][preferences]'), ($tab == 3 ? 'TAB_ACTIVE' : 'TAB_NORMAL'), 'self.setTab(3);'));
			}
		}

		$we_tabs->onResize();
		$tab_header = $we_tabs->getHeader();
		$tab_body = $we_tabs->getJS();

		$out = we_html_element::jsElement('
var activeTab = 0;
function setTab(tab) {
	switch(tab) {
		case 0:
			top.content.user_resize.user_right.user_editor.user_properties.switchPage(0);
			activeTab = 0;
			break;
		case 1:
			if(top.content.user_resize.user_right.user_editor.user_properties.switchPage(1)==false){
				setTimeout("resetTabs()",50);
			}
			activeTab = 1;
			break;
		case 2:
			if(top.content.user_resize.user_right.user_editor.user_properties.switchPage(2)==false) {
				setTimeout("resetTabs()",50);
			}
			activeTab = 2;
			break;
		case 3:
			if(top.content.user_resize.user_right.user_editor.user_properties.switchPage(3)==false) {
				setTimeout("resetTabs()",50);
			}
			activeTab = 3;
			break;
	}
}

function resetTabs(){
		top.content.user_resize.user_right.user_editor.user_properties.document.we_form.tab.value = 0;
		top.content.user_resize.user_right.user_editor.user_edheader.tabCtrl.setActiveTab(0);
}

top.content.hloaded=1;') .
			$tab_header;

		switch($this->Type){
			case self::TYPE_USER_GROUP:
				$headline1 = g_l('modules_users', '[group]') . ': ';
				break;
			case self::TYPE_ALIAS:
				$headline1 = g_l('javaMenu_users', '[menu_alias]') . ': ';
				break;
			default:
				$headline1 = g_l('javaMenu_users', '[menu_user]') . ': ';
		}
		$headline2 = empty($this->Path) ? $this->getPath($this->ParentID) : $this->Path;
		$out .= '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", $headline1) . '&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $headline2) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) . $we_tabs->getHTML() . '</div>' .
			$tab_body;
		return $out;
	}

	/**
	 *
	 * @param type $useSalt DB-field
	 * @param type $username DB-field
	 * @param type $password DB-field!!! //needs to be cause of salt!
	 * @param type $clearPassword //posted password
	 */
	static function comparePasswords($useSalt, $username, $password, $clearPassword){
		switch($useSalt){
			default:
			case 0:
				$passwd = md5($clearPassword);
				break;
			case 1:
				$passwd = md5($clearPassword . md5($username));
				break;
			case 2:
				$passwd = crypt($clearPassword, $password);
				break;
		}
		return ($passwd == $password);
	}

	static function makeSaltedPassword(&$useSalt, $username, $passwd, $strength = 15){
		$WE_SALTCHARS = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		if(version_compare(PHP_VERSION, '5.3.7') >= 0){
			$salt = '$2y$' . sprintf('%02d', $strength) . '$'; //15 rounds
			for($i = 0; $i <= 21; $i++){
				$tmp_str = str_shuffle($WE_SALTCHARS);
				$salt .= $tmp_str[0];
			}
			$useSalt = 2;
			return crypt($passwd, $salt);
		} else{
			$useSalt = 1;
			return md5($passwd . md5($username));
		}
	}

	static function readPrefs($id, $db, $login = false){
//set defaults
		$ret = array('userID' => $id);
		require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');
		foreach($GLOBALS['configs']['user'] as $key => $vals){
			$ret[$key] = $vals[0];
		}
		if($login){
			$db->query('DELETE FROM ' . PREFS_TABLE . ' WHERE `key` NOT IN("' . implode('","', array_keys($GLOBALS['configs']['user'])) . '")');
		}
		$db->query('SELECT `key`,`value` FROM ' . PREFS_TABLE . ' WHERE userID=' . intval($id));
		//read db
		while($db->next_record(MYSQL_ASSOC)) {
			$ret[$db->f('key')] = $db->f('value');
		}
		return $ret;
	}

	/** write settings for a user, all default values are applied before data is written
	 * @id int user id to write settings for
	 * @db socket database connection
	 * @data array optional if empty settings of current session are used.
	 */
	static function writePrefs($id, $db, array $data = array()){
		$id = intval($id);
		if($data){
			$old = array('userID' => $id);
			require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');
			foreach($GLOBALS['configs']['user'] as $key => $vals){
				$old[$key] = $vals[0];
			}
		} else{
			$old = self::readPrefs($id, $db);
			$data = $_SESSION['prefs'];
		}
		$upd = array();
		foreach($old as $key => $val){
			if($key != 'userID' && (!isset($data[$key]) || $data[$key] != $val)){
				$upd[] = '(' . $id . ',"' . $db->escape($key) . '","' . $db->escape((isset($data[$key]) ? $data[$key] : $val)) . '")';
			}
		}
		if(!empty($upd)){
			$db->query('REPLACE INTO ' . PREFS_TABLE . ' (`userID`,`key`,`value`) VALUES ' . implode(',', $upd));
		}
	}

}