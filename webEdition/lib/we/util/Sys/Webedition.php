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
 * @subpackage we_util_Sys
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
/**
 * @see we_util_Sys_Exception
 */
Zend_Loader::loadClass('we_util_Sys');

/**
 * @see we_util_Sys_Exception
 */
Zend_Loader::loadClass('we_util_Sys_Exception');

/**
 * Class to check webEdition settings and installation properties
 *
 * @category   we
 * @package    we_util
 * @subpackage we_util_Sys
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Sys_Webedition extends we_util_Sys{

	/**
	 * tries to identify the version of the currently installed webEdition
	 * @return version string without dots (i.e. "5501") or false, if the version could not be identified.
	 */
	public static function version(){
		if(!defined("WE_VERSION")){
			try{
				include_once $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_version.php";
			} catch (Exception $e){
				/**
				 * @see we_util_Sys_Exception
				 */
				throw new we_util_sys_Exception('Could not identify webEdition version because we_version.inc.php '
					. 'is not available.');
			}

			if(!defined("WE_VERSION"))
				return false;
		}
		return WE_VERSION;
	}

	/**
	 * compares specified webEdition version with the currently installed webEdition version
	 * @param int $reference target version to be compared to current webEdition version
	 * @param string $operator
	 * @see we_util_Sys::_versionCompare()
	 * @example we_util_Sys_Webedition::versionCompare("5501");
	 * @example we_util_Sys_Webedition::versionCompare("5501", "<");
	 */
	public static function versionCompare($version = "", $operator = ""){
		$currentVersion = self::version();
		if($currentVersion === false || empty($version))
			return false;
		return parent::_versionCompare($version, $currentVersion, $operator);
	}

	/**
	 * checks if a requested module is installed and / or active
	 * @param string module name
	 * @return int
	 * 		-1	module not installed or an error occured on fetching module installation informations from webEdition
	 * 		0	module installed but inactive (only available for integrated modules)
	 * 		1	module installed and active
	 */
	public static function module($property = ""){
		if(empty($property))
			return -1;

		// all modules available for webEdition:
		try{
			include_once $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_available_modules.inc.php";
		} catch (Exception $e){
			/**
			 * @see we_util_Sys_Exception
			 */
			throw new we_util_sys_Exception('could not read module information from we_available_modules.inc.php.');
			return -1;
		}
		if(!in_array($property, $_we_available_modules)){
			return -1;
		}

		// integrated modules (free of charge, can be deactivated in webEdition preferences):
		// users, schedule, editor, banner, export, voting, spellchecker, glossary
		try{
			include_once(WE_INCLUDES_PATH . "conf/we_active_integrated_modules.inc.php");
		} catch (Exception $e){
			throw new we_util_sys_Exception('could not read module information from we_active_integrated_modules.inc.php.');
			return -1;
		}

		if(in_array($property, $GLOBALS['_we_active_integrated_modules'])){
			return 1;
		} else{
			return 0;
		}
	}

	/**
	 * builds a list of all installed modules (active and inactive) and returns it to the caller
	 *
	 * @return array a list of all installed webEdition modules or (bool)false, if an error occured
	 */
	public static function modulesInstalled(){
		// not implemented yet
		return array();
	}

	/**
	 * builds a list of all activated modules (including free and formerly non-free modules) and returns it to the caller
	 *
	 * @return array a list of all active webEdition modules or (bool)false, if an error occured
	 */
	public static function modulesActive(){
		// not implemented yet
		return array();
	}

	/**
	 * builds a list of all available modules and returns it to the caller
	 *
	 * @return array a list of all available webEdition modules or (bool)false, if an error occured
	 */
	public static function modulesAvailable(){
		try{
			include_once $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_available_modules.inc.php";
		} catch (Exception $e){
			/**
			 * @see we_util_Sys_Exception
			 */
			throw new we_util_sys_Exception('could not read module information from we_available_modules.inc.php.');
			return false;
		}
		return $_we_available_modules;
	}

	/**
	 * checks if a requested tool is installed
	 * this implementation is preliminary and WILL be changed once the we_tool-implementation is completed
	 * @param string tool name
	 * @return false (not installed) or true (installed)
	 */
	public static function tool($property = ""){
		if(empty($property))
			return false;
		$tooldir = $_SERVER['DOCUMENT_ROOT'] . "/webEdition/apps/" . $property;
		try{
			if(is_dir($tooldir) && is_readable($tooldir)){
				return true;
			}
		} catch (Exception $e){
			throw new we_util_sys_Exception('The tool installation path does not exist.');
			return false;
		}
	}

	/**
	 * get the version of the requested tool (if it is installed)
	 * @param string tool name
	 * @return string version
	 */
	public static function toolVersion($property = ""){
		// not imlpemented yet
		return "1.0";
	}

	/**
	 * compares specified tool version with the currently installed version of this tool
	 * @param string tool name
	 * @param int $reference target version to be compared to currently installed tool version
	 * @param string $operator
	 * @see we_util_Sys::_versionCompare()
	 * @example we_util_Sys_Webedition::toolVersionCompare("5.1");
	 * @example we_util_Sys_Webedition::toolVersionCompare("5.1", "<");
	 */
	public static function toolVersionCompare($property = "", $reference = "", $operator = ""){
		if(empty($property) || empty($reference)){
			return false;
		} else{
			$version = self::toolVersion($property);
		}
		if($version === false){
			return false;
		} else{
			return parent::_versionCompare($reference, $version, $operator);
		}
	}

	public static function toolsInstalled(){
		// not implemented yet
		return array();
	}

}