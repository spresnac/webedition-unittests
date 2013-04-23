<?php

/**
 * webEdition CMS
 *
 * $Rev: 5565 $
 * $Author: mokraemer $
 * $Date: 2013-01-14 10:19:02 +0100 (Mon, 14 Jan 2013) $
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
abstract class weToolLookup{

	const REGISTRY_NAME = 'weToolsRegistry';

	static function getAllTools($force = false, $addInternTools = false, $includeDisabled = false){

		if(!$force && !$includeDisabled && !defined('NO_SESS') && isset($_SESSION[self::REGISTRY_NAME]['meta'])){
			return $_SESSION[self::REGISTRY_NAME]['meta'];
		}
		if(!$force && $includeDisabled && !defined('NO_SESS') && isset($_SESSION[self::REGISTRY_NAME]['metaIncDis'])){
			return $_SESSION[self::REGISTRY_NAME]['metaIncDis'];
		}

		$_tools = array();

		$_toolsDirs = array();

		//$_ignore = array('','.','..','cvs','cache','search','first_steps_wizard','weSearch','navigation');

		/*
		  if($addInternTools) {
		  $addSearch = current(array_keys($_ignore, 'weSearch'));
		  unset($_ignore[$addSearch]);
		  $addNavigation = current(array_keys($_ignore, 'navigation'));
		  unset($_ignore[$addNavigation]);
		  }
		 */

		$_bd = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps';
		$_d = opendir($_bd);

		while($_entry = readdir($_d)) {
			//if( !in_array($_entry,$_ignore) && is_dir($_bd . '/' . $_entry)){
			$_toolsDirs[] = $_bd . '/' . $_entry;
			//}
		}
		closedir($_d);

		// include autoload function
		include_once($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');

		$lang = isset($GLOBALS['WE_LANGUAGE']) ? $GLOBALS['WE_LANGUAGE'] : we_core_Local::getComputedUILang();
		Zend_Loader::loadClass('we_core_Local');


		foreach($_toolsDirs as $_toolDir){
			$_metaFile = $_toolDir . '/conf/meta.conf.php';
			if(is_dir($_toolDir) && file_exists($_metaFile)){
				include($_metaFile);
				if(isset($metaInfo)){
					$langStr = '';
					if(isset($metaInfo['name'])){
						$translate = we_core_Local::addTranslation('default.xml', $metaInfo['name']);
						if(is_object($translate)){
							$langStr = $translate->_($metaInfo['name']);
						}
					}
					$metaInfo['text'] = oldHtmlspecialchars($langStr);
					if(!$includeDisabled && isset($metaInfo['appdisabled']) && $metaInfo['appdisabled']){

					} else{
						$_tools[] = $metaInfo;
					}
					unset($metaInfo);
				}
			}
		}
		if($addInternTools){

			$internToolDir = WE_INCLUDES_PATH . 'we_tools/';
			$internTools = array('weSearch', 'navigation');

			foreach($internTools as $_toolName){
				$_metaFile = $internToolDir . $_toolName . '/conf/meta.conf.php';
				if(file_exists($_metaFile)){
					include($_metaFile);
					if(isset($metaInfo)){
						$metaInfo['text'] = $metaInfo['name'];
						$_tools[] = $metaInfo;
						unset($metaInfo);
					}
				}
			}
		}

		if(!defined('NO_SESS') && !$includeDisabled){
			$_SESSION[self::REGISTRY_NAME]['meta'] = $_tools;
		}
		if(!defined('NO_SESS') && $includeDisabled){
			$_SESSION[self::REGISTRY_NAME]['metaIncDis'] = $_tools;
		}

		return $_tools;
	}

	static function getToolProperties($name){

		$_tools = weToolLookup::getAllTools(true, false, true);

		foreach($_tools as $_tool){
			if($_tool['name'] == $name){
				return $_tool;
			}
		}
		return array();
	}

	static function getPhpCmdInclude(){

		if(isset($_REQUEST['we_cmd'][0])){
			//FIX for charset in tools, due to not started session
			$tmp = explode('_', $_REQUEST['we_cmd'][0]);
			if($tmp[1] == 'weSearch' || $tmp[1] == 'navigation'){
				$_REQUEST['tool'] = $tmp[1];
				return 'we_tools/' . $tmp[1] . '/hook/we_phpCmdHook_' . $tmp[1] . '.inc.php';
			}
			$_tools = weToolLookup::getAllTools(true, true);
			foreach($_tools as $_tool){
				if(stripos($_REQUEST['we_cmd'][0], 'tool_' . $_tool['name'] . '_') === 0){
					$_REQUEST['tool'] = $_tool['name'];
					if($_REQUEST['tool'] == 'weSearch' || $_REQUEST['tool'] == 'navigation'){
						return 'we_tools/' . $_tool['name'] . '/hook/we_phpCmdHook_' . $_tool['name'] . '.inc.php';
					} else{
						return 'apps/' . $_tool['name'] . '/hook/we_phpCmdHook_' . $_tool['name'] . '.inc.php';
					}
					break;
				}
			}
		}

		return '';
	}

	static function getJsCmdInclude(){

		$_inc = array();
		$_tools = weToolLookup::getAllTools(true, true);
		foreach($_tools as $_tool){
			if(($_tool['name'] == 'weSearch' || $_tool['name'] == 'navigation') && file_exists(WE_INCLUDES_PATH . 'we_tools/' . $_tool['name'] . '/hook/we_jsCmdHook_' . $_tool['name'] . '.inc.php')){
				$_inc[] = WE_INCLUDES_PATH . 'we_tools/' . $_tool['name'] . '/hook/we_jsCmdHook_' . $_tool['name'] . '.inc.php';
			} elseif(file_exists(WEBEDITION_PATH . 'apps/' . $_tool['name'] . '/hook/we_jsCmdHook_' . $_tool['name'] . '.inc.php')){
				$_inc[] = WEBEDITION_PATH . 'apps/' . $_tool['name'] . '/hook/we_jsCmdHook_' . $_tool['name'] . '.inc.php';
			}
		}

		return $_inc;
	}

	static function getDefineInclude(){

		if(!defined('NO_SESS') && isset($_SESSION[self::REGISTRY_NAME]['defineinclude'])){
			return $_SESSION[self::REGISTRY_NAME]['defineinclude'];
		}

		$_inc = array();
		$_tools = weToolLookup::getAllTools();
		foreach($_tools as $_tool){
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/conf/define.conf.php')){
				$_inc[] = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/conf/define.conf.php';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION[self::REGISTRY_NAME]['defineinclude'] = $_inc;
		}

		return $_inc;
	}

	function getExternTriggeredTasks(){

		if(!defined('NO_SESS') && isset($_SESSION[self::REGISTRY_NAME]['ExternTriggeredTasks'])){
			//return $_SESSION[self::REGISTRY_NAME]['ExternTriggeredTasks'];
		}

		$_inc = array();
		$_tools = weToolLookup::getAllTools();
		foreach($_tools as $_tool){
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/externtriggered/tasks.php') && we_app_Common::isActive($_tool['name'])){
				$_inc[] = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/externtriggered/tasks.php';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION[self::REGISTRY_NAME]['ExternTriggeredTasks'] = $_inc;
		}

		return $_inc;
	}

	static function getTagDirs(){

		if(!defined('NO_SESS') && isset($_SESSION[self::REGISTRY_NAME]['tagdirs'])){
			return $_SESSION[self::REGISTRY_NAME]['tagdirs'];
		}

		$_inc = array();
		$_tools = weToolLookup::getAllTools();
		foreach($_tools as $_tool){
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/tags')){
				$_inc[] = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/tags';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION[self::REGISTRY_NAME]['tagdirs'] = $_inc;
		}

		return $_inc;
	}

	static function isActiveTag($filepath){
		return in_array(dirname($filepath), weToolLookup::getTagDirs());
	}

	static function isTool($name, $includeDisabled = false){
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if($_tool['name'] == $name){
				return true;
			}
		}
		return in_array($name, $_tools);
	}

	static function getCmdInclude($namespace, $name, $cmd){
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		return $toolFolder . $name . '/service/cmds' . $namespace . 'rpc' . $cmd . 'Cmd.class.php';
	}

	static function getViewInclude($protocol, $namespace, $name, $view){
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		return $toolFolder . $name . '/service/views/' . $protocol . $namespace . 'rpc' . $view . 'View.class.php';
	}

	static function getAllToolTags($toolname, $includeDisabled = false){
		return weToolLookup::getFileRegister($toolname, '/tags', 'we_tag_', 'we_tag_', '.inc.php', $includeDisabled);
	}

	static function getAllToolTagWizards($toolname, $includeDisabled = false){
		return weToolLookup::getFileRegister($toolname, '/tagwizard', 'we_tag_', 'we_tag_', '.inc.php', $includeDisabled);
	}

	static function getAllToolServices($toolname, $includeDisabled = false){
		return weToolLookup::getFileRegister($toolname, '/service/cmds', '^rpc', 'rpc', 'Cmd.class.php', $includeDisabled);
	}

	static function getAllToolLanguages($toolname, $subdir = '/lang', $includeDisabled = false){

		$_founds = array();
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		$_tooldir = $toolFolder . $toolname . $subdir;
		if(weToolLookup::isTool($toolname, $includeDisabled) && is_dir($_tooldir)){
			$_d = opendir($_tooldir);
			while($_entry = readdir($_d)) {
				if(is_dir($_tooldir . '/' . $_entry) && stristr($_entry, '.') === FALSE){
					$_tagname = we_core_Local::localeToWeLang($_entry);
					$_founds[$_tagname] = $_tooldir . '/' . $_entry . '/default.xml';
				}
			}
			closedir($_d);
		}
		return $_founds;
	}

	static function getFileRegister($toolname, $subdir, $filematch, $rem_before = '', $rem_after = '', $includeDisabled = false){
		$_founds = array();
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		$_tooldir = $toolFolder . $toolname . $subdir;
		if(weToolLookup::isTool($toolname, $includeDisabled) && is_dir($_tooldir)){
			$_d = opendir($_tooldir);
			while($_entry = readdir($_d)) {
				if(!is_dir($_tooldir . '/' . $_entry) && stripos($_entry, $filematch) !== false){
					$_tagname = str_replace($rem_before, '', $_entry);
					$_tagname = str_replace($rem_after, '', $_tagname);
					$_founds[$_tagname] = $_tooldir . '/' . $_entry;
				}
			}
			closedir($_d);
		}
		return $_founds;
	}

	static function getToolTag($name, &$include, $includeDisabled = false){
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		foreach($_tools as $_tool){
			if(file_exists($toolFolder . $_tool['name'] . '/tags/we_tag_' . $name . '.inc.php')){
				$include = $toolFolder . $_tool['name'] . '/tags/we_tag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewTag($name, &$include, $includeDisabled = false){
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		foreach($_tools as $_tool){
			if(file_exists($toolFolder . $_tool['name'] . '/tags/we_listviewtag_' . $name . '.inc.php')){
				$include = $toolFolder . $_tool['name'] . '/tags/we_listviewtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewItemTag($name, &$include, $includeDisabled = false){
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		foreach($_tools as $_tool){
			if(file_exists($toolFolder . $_tool['name'] . '/tags/we_listviewitemtag_' . $name . '.inc.php')){
				$include = $toolFolder . $_tool['name'] . '/tags/we_listviewitemtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolTagWizard($name, &$include, $includeDisabled = false){
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		foreach($_tools as $_tool){
			if(file_exists($toolFolder . $_tool['name'] . '/tagwizard/we_tag_' . $name . '.inc.php')){
				$include = $toolFolder . $_tool['name'] . '/tagwizard/we_tag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewTagWizard($name, &$include, $includeDisabled = false){
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		$toolFolder = defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/';
		foreach($_tools as $_tool){
			if(file_exists($toolFolder . $_tool['name'] . '/tagwizard/we_listviewtag_' . $name . '.inc.php')){
				$include = $toolFolder . $_tool['name'] . '/tagwizard/we_listviewtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getPermissionIncludes($includeDisabled = false){
		$_inc = array();
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/conf/permission.conf.php')){
				$_inc[] = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/conf/permission.conf.php';
			}
		}

		return $_inc;
	}

	static function getToolsForBackup($includeDisabled = false){
		$_inc = array();
		$_tools = weToolLookup::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/' . $_tool['name'] . '/conf/backup.conf.php')){
				if($_tool['maintable'] != ''){
					$_inc[] = $_tool['name'];
				}
			}
		}
		$_inc[] = 'weSearch';


		return $_inc;
	}

	static function getBackupTables($name){
		$toolFolder = (($name == 'weSearch' || $name == 'navigation') ?
				WE_INCLUDES_PATH . 'we_tools/' :
				(defined('WE_APPS_PATH') ? WE_APPS_PATH : $GLOBALS['__WE_APP_PATH__'] . '/'));
		if(file_exists($toolFolder . $name . '/conf/backup.conf.php')){
			include($toolFolder . $name . '/conf/backup.conf.php');
			if(!empty($toolTables)){
				return $toolTables;
			}
		}
		return array();
	}

	static function getFilesOfDir(&$allFiles, $baseDir){

		if(file_exists($baseDir)){

			$dh = opendir($baseDir);
			while($entry = readdir($dh)) {

				if($entry != '' && $entry != '.' && $entry != '..'){

					$_entry = $baseDir . '/' . $entry;

					if(!is_dir($_entry)){
						$allFiles[] = $_entry;
					}

					if(is_dir($_entry) && strtolower(strtolower($entry) != 'cvs')){
						weToolLookup::getFilesOfDir($allFiles, $_entry);
					}
				}
			}
			closedir($dh);
		}
	}

	static function getDirsOfDir(&$allDirs, $baseDir){

		if(file_exists($baseDir)){

			$dh = opendir($baseDir);
			while($entry = readdir($dh)) {

				if($entry != '' && $entry != '.' && $entry != '..' && strtolower($entry != 'cvs')){

					$_entry = $baseDir . '/' . $entry;

					if(is_dir($_entry)){
						$allDirs[] = $_entry;
						weToolLookup::getDirsOfDir($allDirs, $_entry);
					}
				}
			}
			closedir($dh);
		}
	}

	static function getIgnoreList(){
		return array('doctype', 'category', 'navigation', 'toolfactory', 'weSearch');
	}

	static function isInIgnoreList($toolname){
		$_ignore = weToolLookup::getIgnoreList();
		return in_array($toolname, $_ignore);
	}

	static function getModelClassName($name){
		if($name == 'weSearch' || $name == 'navigation'){
			include(WE_INCLUDES_PATH . 'we_tools/' . $name . '/conf/meta.conf.php');
			return $metaInfo['classname'];
		}

		$_tool = weToolLookup::getToolProperties($name);
		if(!empty($_tool)){
			return $_tool['classname'];
		}

		return '';
	}

}