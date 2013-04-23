<?php

/**
 * webEdition CMS
 *
 * $Rev: 5855 $
 * $Author: mokraemer $
 * $Date: 2013-02-20 21:36:29 +0100 (Wed, 20 Feb 2013) $
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
function we_tag_url($attribs){
	$foo = attributFehltError($attribs, "id", __FUNCTION__);
	if($foo)
		return $foo;
	static $urls = array();
	static $objurls = array();
	$type = weTag_getAttribute("type", $attribs, 'document');
	$id = weTag_getAttribute("id", $attribs);
	$triggerid = weTag_getAttribute("triggerid", $attribs, '0');
	$hidedirindex = weTag_getAttribute("hidedirindex", $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
	$objectseourls = weTag_getAttribute("objectseourls", $attribs, TAGLINKS_OBJECTSEOURLS, true);
	if($type == 'document'){
		if(isset($urls[$id])){ // do only work you have never done before
			return $urls[$id];
		}
	} else{
		if(isset($objurls[$id])){ // do only work you have never done before
			return $objurls[$id];
		}
	}
	if($id == '0'){
		$url = "/";
	} else{
		$urlNotSet = true;
		if(($id == 'self' || $id == 'top') && $type == 'document'){
			$doc = we_getDocForTag($id, true); // check if we should use the top document or the  included document
			$testid = $doc->ID;
			if($id == 'top'){//check for object
				if(isset($GLOBALS['WE_MAIN_DOC']->TableID)){//ein object
					if(!$triggerid){
						$triggerid = $GLOBALS['WE_MAIN_DOC']->ID;
					}
					$path_parts = pathinfo(id_to_path($triggerid));
					if($objectseourls && $GLOBALS['WE_MAIN_DOC']->Url != '' && show_SeoLinks()){
						$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
							(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								'' : $path_parts['filename'] . '/') .
							$GLOBALS['WE_MAIN_DOC']->Url;
					} else{
						$url = (show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . "?we_objectID=" . $GLOBALS['WE_MAIN_DOC']->OF_ID :
								$GLOBALS['WE_MAIN_DOC']->Path . "?we_objectID=" . $GLOBALS['WE_MAIN_DOC']->OF_ID);
					}
					$urlNotSet = false;
				}
			}
		} else{
			$testid = $id;
		}
		if($urlNotSet){
			if($type == 'document'){
				$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID=' . intval($testid), $GLOBALS['DB_WE']);
				$url = isset($row["Path"]) ? ($row["Path"] . ($row["IsFolder"] ? "/" : "")) : "";
				$path_parts = pathinfo($url);
				if(show_SeoLinks() && $hidedirindex && NAVIGATION_DIRECTORYINDEX_NAMES != '' && TAGLINKS_DIRECTORYINDEX_HIDE && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}
			} else{
				$row = getHash('SELECT ID,Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($testid), $GLOBALS['DB_WE']);
				if(!$triggerid){
					$triggerid = ($row['TriggerID'] ? $row['TriggerID'] : $GLOBALS['WE_MAIN_DOC']->ID);
				}
				$path_parts = pathinfo(id_to_path($triggerid));
				if($objectseourls && $row['Url'] != '' && show_SeoLinks()){
					$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
						(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
							'' : $path_parts['filename'] . '/' ) .
						$row['Url'];
				} else{
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . "?we_objectID=" . $row['ID'];
					} else{
						$url = id_to_path($triggerid) . "?we_objectID=" . $row['ID'];
					}
				}
			}
		}
	}
	if($type == 'document'){
		$urls[$id] = $url;
	} else{
		$objurls[$id] = $url;
	}
	return $url;
}