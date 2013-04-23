<?php

/**
 * webEdition CMS
 *
 * $Rev: 4344 $
 * $Author: mokraemer $
 * $Date: 2012-03-24 22:55:36 +0100 (Sat, 24 Mar 2012) $
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
class weNavigationCache{

	const CACHEDIR = '/webEdition/we/include/we_tools/navigation/cache/';

	static $rebuildRootCnt = 0;

	static function delNavigationTree($id){
		static $deleted = array();
		if(in_array($id, $deleted)){
			return;
		}
		self::delCacheNavigationEntry(0);
		//self::cacheRootNavigation();
		$_id = $id;
		$_c = 0;
		while($_id != 0) {
			self::delCacheNavigationEntry($_id);
			$deleted[] = $_id;
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), 'ParentID', new DB_WE());
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	static function cacheNavigationTree($id){
		weNavigationCache::cacheNavigationBranch($id);
		//weNavigationCache::cacheRootNavigation();
	}

	static function cacheNavigationBranch($id){
		$_id = $id;
		$_c = 0;
		$db = new DB_WE();
		while($_id != 0) {
			self::cacheNavigation($_id);
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), 'ParentID', $db);
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	/* no need for this.
	 * static function cacheRootNavigation(){
	  if(!self::$rebuildRootCnt++){
	  return;
	  }
	  $_naviItemes = new weNavigationItems();

	  $_naviItemes->initById(0);

	  self::saveCacheNavigation(0, $_naviItemes);

	  $currentRulesStorage = $_naviItemes->currentRules; // Bug #4142
	  foreach($currentRulesStorage as &$rule){
	  $rule->deleteDB();
	  }
	  $_content = serialize($currentRulesStorage);
	  unset($currentRulesStorage);

	  weFile::save($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'rules.php', $_content);
	  } */

	static function cacheNavigation($id){
		$_naviItemes = new weNavigationItems();
		$_naviItemes->initById($id);
		self::saveCacheNavigation($id, $_naviItemes);
	}

	static function delCacheNavigationEntry($id){
		weFile::delete($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $id . '.php');
	}

	static function saveCacheNavigation($id, $_naviItemes){
		weFile::save($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $id . '.php', gzdeflate(serialize($_naviItemes->items), 9));
	}

	static function getCacheFromFile($parentid){
		$_cache = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $parentid . '.php';

		if(file_exists($_cache)){
			return @unserialize(@gzinflate(weFile::load($_cache)));
		}
		return false;
	}

	static function getCachedRule(){
		$_cache = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'rules.php';
		if(file_exists($_cache)){
			return $navigationRulesStorage = weFile::load($_cache);
		}
		return false;
	}

	/**
	 * Used on upgrade to remove all navigation entries
	 */
	static function clean($force = false){
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'clean')){
			unlink($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'clean');
			$force = true;
		}
		if($force){
			$files = scandir($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR);
			foreach($files as $file){
				if(strpos($file, 'navigation_') === 0){
					unlink($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . $file);
				}
			}
		}
	}

}