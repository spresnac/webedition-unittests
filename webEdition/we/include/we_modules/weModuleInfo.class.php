<?php

abstract class weModuleInfo {

	static function _orderModules($a, $b){
    	return (strcmp($a["text"],$b["text"]));

	}

	/**
	 * Orders a hash array of the scheme of we_available_modules
	 *
	 * @param hash $array
	 */
	static function orderModuleArray(&$array) {
		uasort($array, array("weModuleInfo","_orderModules"));

	}

	/**
	 * returns hash with All modules
	 *
	 * @return hash
	 */
	static function getAllModules() {
		global $_we_available_modules;

		$retArr = array();

		foreach ($_we_available_modules as $key => $modInfo) {
				$retArr[$key] = $modInfo;
		}

		return $retArr;
	}


	/**
	 * returns hash with all buyable modules
	 *
	 * @return hash
	 */
	static function getNoneIntegratedModules() {
		global $_we_available_modules;

		$retArr = array();

		foreach ($_we_available_modules as $key => $modInfo) {
			if ($modInfo["integrated"] == false) {
				$retArr[$key] = $modInfo;
			}
		}

		return $retArr;
	}

	/**
	 * @param string $mKey
	 * @return boolean
	 */
	static function isModuleInstalled($mKey) {

		if (in_array($mKey, $GLOBALS['_we_active_integrated_modules']) || $mKey == "editor") {
			return true;
		}

		return false;
	}

	/**
	 * returns hash of all integrated modules
	 * @return hash
	 */
	static function getIntegratedModules($active=null) {

		global $_we_available_modules;

		$retArr = array();

		foreach ($_we_available_modules as $key => $modInfo) {
			if ($modInfo["integrated"] == true) {

				if ($active === null) {
					$retArr[$key] = $modInfo;

				} else if ( in_array($key, $GLOBALS['_we_active_integrated_modules']) == $active ) {
					$retArr[$key] = $modInfo;
				}
			}
		}

		return $retArr;
	}

	/**
	 * returns whether a module is in the menu or not
	 * @param string $modulekey
	 * @return boolean
	 */
	static function showModuleInMenu($modulekey) {
		global $_we_available_modules;
		/*
		if ($_we_available_modules[$modulekey]["integrated"]) {
			return true;

		} else {
		*/
			// show a module, if
			// - it is active
			// - if it is in module window

			if ( $_we_available_modules[$modulekey]["inModuleMenu"] && in_array($modulekey, $GLOBALS["_we_active_integrated_modules"]) ) {
				return true;
			}

		//}

		return false;
	}

	static function isActive($modul) {
		return in_array($modul,$GLOBALS['_we_active_integrated_modules']);
	}
}
