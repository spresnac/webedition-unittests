<?php

/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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
class weTagWizard{

	static function getExistingWeTags(){
		$retTags = array();
		$main = self::getMainTagModules();
		foreach($main as $modulename => $tags){

			if($modulename == 'basis' || $modulename == 'navigation' || in_array($modulename, $GLOBALS['_we_active_integrated_modules'])){
				$retTags = array_merge($retTags, $tags);
			}
		}

		// add custom tags
		$retTags = array_merge($retTags, self::getCustomTags());

		// add application tags
		$retTags = array_merge($retTags, self::getApplicationTags());
		natcasesort($retTags);
		self::initTagLists($retTags);
		return array_values($retTags);
	}

	static function getWeTagGroups($allTags = array()){
		//initTagList
		$tags = self::getExistingWeTags();
		$cache = getWEZendCache();
		return $cache->load('TagWizard_groups');


		$taggroups = array();
		$main = self::getMainTagModules();
		// 1st make grps based on modules
		foreach($main as $modulename => $tags){

			if($modulename == 'basis'){
				$taggroups['alltags'] = $tags;
			}

			if(in_array($modulename, $GLOBALS['_we_active_integrated_modules'])){
				$taggroups[$modulename] = $tags;
				$taggroups['alltags'] = array_merge($taggroups['alltags'], $tags);
			}
		}
		//add applicationTags
		$apptags = weTagWizard::getApplicationTags();
		if(!empty($apptags)){
			$taggroups['apptags'] = $apptags;
			$taggroups['alltags'] = array_merge($taggroups['alltags'], $taggroups['apptags']);
		}


		// 2nd add some taggroups to this array
		if(empty($allTags)){
			$allTags = weTagWizard::getExistingWeTags();
		}
		foreach($GLOBALS['tag_groups'] as $key => $tags){

			for($i = 0; $i < count($tags); $i++){
				if(in_array($tags[$i], $allTags)){
					$taggroups[$key][] = $tags[$i];
				}
			}
		}

		// at last add custom tags.
		$customTags = weTagWizard::getCustomTags();
		if(!empty($customTags)){
			$taggroups['custom'] = $customTags;
			$taggroups['alltags'] = array_merge($taggroups['alltags'], $taggroups['custom']);
		}

		natcasesort($taggroups['alltags']);
		return $taggroups;
	}

	static function getMainTagModules(){
		$cache = getWEZendCache();
		if(!($main = $cache->load('TagWizard_mainTags'))){
			$main = array();
			$tags = self::getTagsFromDir(WE_INCLUDES_PATH . 'weTagWizard/we_tags/');
			foreach($tags as $tagname){
				$tag = weTagData::getTagData($tagname);
				$main[$tag->getModule()][] = $tagname;
			}
			$cache->save($main);
		}
		return $main;
	}

	/**
	 * Initializes database for all tags
	 */
	static function initTagLists($tags){
		$cache = getWEZendCache(24*3600);
		if(($count = $cache->load('TagWizard_tagCount')) && (count($tags) == $count)){
			return;
		}
		$endTags = array();
		$modules = array();
		$groups = array();
		foreach($tags as $tagname){
			$tag = weTagData::getTagData($tagname);
			$mod = $tag->getModule();
			$modules[$mod][] = $tagname;
			$groups['alltags'][] = $tagname;
			if($mod != 'basis'){
				$groups[$mod][] = $tagname;
			}
			foreach($tag->getGroups() as $group){
				$groups[$group][] = $tagname;
			}
			if($tag->needsEndTag()){
				$endTags[] = $tagname;
			}
		}
		$cache->save(count($tags), 'TagWizard_tagCount');
		$cache->save($endTags, 'TagWizard_needsEndTag');
		$cache->save($groups, 'TagWizard_groups');
		$cache->save($modules, 'TagWizard_modules');
	}

	//FIXME: check if custom tags are updated correctly!
	static function getTagsWithEndTag(){
		$cache = getWEZendCache(24*3600);
		if(!($tags = $cache->load('TagWizard_needsEndTag'))){
			self::getExistingWeTags();
			$tags = $cache->load('TagWizard_needsEndTag');
		}
		return $tags;
	}

	static function getCustomTags(){
		$cache = getWEZendCache();
		if(!($customTags = $cache->load('TagWizard_customTags'))){
			$customTags = self::getTagsFromDir(WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags');
			$cache->save($customTags);
		}
		return $customTags;
	}

	static function getTagsFromDir($dir){
		$ret = array();
		if(is_dir($dir)){

			// get the custom tag-descriptions
			$handle = dir($dir);

			while(false !== ($entry = $handle->read())) {

				if(preg_match("/we_tag_(.*).inc.php/", $entry, $match)){
					$ret[] = $match[1];
				}
			}
		}
		return $ret;
	}

	static function getApplicationTags(){

		if(!isset($GLOBALS['weTagWizard_applicationTags'])){

			$GLOBALS['weTagWizard_applicationTags'] = array();
			$apptags = array();
			$alltools = weToolLookup::getAllTools(true);
			foreach($alltools as $tool){
				$apptags = weToolLookup::getAllToolTagWizards($tool['name']);
				$apptagnames = array_keys($apptags);
				$GLOBALS['weTagWizard_applicationTags'] = array_merge($GLOBALS['weTagWizard_applicationTags'], $apptagnames);
			}
		}
		return $GLOBALS['weTagWizard_applicationTags'];
	}

}