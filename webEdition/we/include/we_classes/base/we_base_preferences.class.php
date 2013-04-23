<?php

/**
 * webEdition CMS
 *
 * $Rev: 5701 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 14:30:37 +0100 (Sat, 02 Feb 2013) $
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
include_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

class we_base_preferences{

	static function loadConfigs(){
		// First, read all needed files
		$GLOBALS['config_files'] = array(
			// we_conf.inc.php
			'conf' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_conf.inc.php',
				'content' => '',
			),
			// we_conf_global.inc.php
			'conf_global' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php',
				'content' => '',
			),
			// proxysettings.inc.php
			'proxysettings' => array(
				'filename' => WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php',
				'content' => '',
			),
			// we_active_integrated_modules.inc.php
			'active_integrated_modules' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php',
				'content' => '',
			),
		);
		foreach($GLOBALS['config_files'] as &$config){
			$config['content'] = weFile::load($config['filename']);
			$config['contentBak'] = $config['content'];
		}
		//finally add old session prefs
		$GLOBALS['config_files']['oldPrefs'] = $_SESSION['prefs'];
	}

	static function setConfigContent($type, $content){
		$GLOBALS['config_files'][$type]['content'] = $content;
	}

	static function unsetConfig($type){
		unset($GLOBALS['config_files'][$type]);
	}

	/**
	 * Checks the global configuration file we_conf_global.inc.php if every needed value
	 * is available and adds missing values.
	 *
	 * @param
	 *
	 * @return         void
	 */
	static function check_global_config($updateVersion = false, $file = '', $leave = array()){
		$values = $GLOBALS['configs']['global'];

		// Read the global configuration file
		$_file_name = WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php';
		$_file_name_backup = $_file_name . '.bak';
		$oldContent = weFile::load($_file_name);

		if($file != '' && $file != $_file_name){
			$_file_name = $file;
			$content = weFile::load($_file_name);
			//leave settings in their current state
			foreach($leave as $settingname){
				$content = self::changeSourceCode('define', $content, $settingname, constant($settingname), true);
			}
		} else{
			$content = $oldContent;
		}
		// load & Cut closing PHP tag from configuration file
		$content = trim(str_replace(array('?>', "\n\n\n\n", "\n\n\n"), array('', "\n\n", "\n\n"), $content), "\n ");
		$oldContent = trim(str_replace('?>', '', $oldContent), "\n ");

		// Go through all needed values
		foreach($values as $define => $value){
			if(!preg_match('/define\(["\']' . $define . '["\'],/', $content)){
				// Add needed variable
				$content = self::changeSourceCode('add', $content, $define, $value[1], true, $value[0]);
				//define it in running session
				if(!defined($define)){
					define($define, $value[1]);
				}
			}
		}
		if($updateVersion){
			$content = self::changeSourceCode('define', $content, 'CONF_SAVED_VERSION', WE_SVNREV, true);
		}
		// Check if we need to rewrite the config file
		if($content != $oldContent){
			weFile::save($_file_name_backup, $oldContent);
			weFile::save($_file_name, $content);
		}
	}

	static function saveConfigs(){
		if(!isset($GLOBALS['configs'])){
			t_e('no config set');
			return;
		}

		foreach($GLOBALS['config_files'] as $file){
			if(isset($file['content']) && $file['content'] != $file['contentBak']){ //only save if anything changed
				weFile::save($file['filename'] . '.bak', $file['contentBak']);
				weFile::save($file['filename'], trim($file['content'], "\n "));
			}
		}

		$tmp = array_diff_assoc($_SESSION['prefs'], $GLOBALS['config_files']['oldPrefs']);
		if(!empty($tmp)){
			we_user::writePrefs($_SESSION['prefs']['userID'], $GLOBALS['DB_WE']);
		}
		unset($GLOBALS['config_files']);
	}

	static function userIsAllowed($setting){
		if(we_hasPerm('ADMINISTRATOR')){
			return true;
		}
		$configs = $GLOBALS['configs'];
		foreach($configs as $name => $config){
			if(isset($config[$setting])){
				switch($name){
					case 'global':
						return (isset($config[$setting][2]) ? we_hasPerm($config[$setting][2]) : we_hasPerm('ADMINISTRATOR'));
					case 'user':
						return true;
					default:
						return (isset($config[$setting][1]) ? we_hasPerm($config[$setting][1]) : we_hasPerm('ADMINISTRATOR'));
				}
			}
		}
	}

	public static function changeSourceCode($type, $text, $key, $value, $active = true, $comment = ''){
		switch($type){
			case 'add':
				return trim($text, "\n\t ") . "\n\n" .
					self::makeDefine($key, $value, $active, $comment);
			case 'define':
				$match = array();
				if(preg_match('|/?/?define\(\s*(["\']' . preg_quote($key) . '["\'])\s*,\s*([^\r\n]+)\);[\r\n]?|Ui', $text, $match)){
					return str_replace($match[0], self::makeDefine($key, $value, $active), $text);
				}
		}

		return $text;
	}

	private static function makeDefine($key, $val, $active = true, $comment = ''){
		return ($comment ? '//' . $comment . "\n" : '') . ($active ? '' : "//") . 'define(\'' . $key . '\', ' .
			(is_bool($val) || $val == 'true' || $val == 'false' ? ($val ? 'true' : 'false') :
				(!is_numeric($val) ? '"' . self::_addSlashes($val) . '"' : intval($val))) . ');';
	}

	private static function _addSlashes($in){
		return str_replace(array("\\", '"', "\$"), array("\\\\", '\"', "\\\$"), $in);
	}

}