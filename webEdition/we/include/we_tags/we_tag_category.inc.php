<?php

/**
 * webEdition CMS
 *
 * $Rev: 5692 $
 * $Author: mokraemer $
 * $Date: 2013-01-31 22:20:18 +0100 (Thu, 31 Jan 2013) $
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
function we_tag_category($attribs){

	// initialize possible Attributes
	$delimiter = weTag_getAttribute('delimiter', $attribs, weTag_getAttribute('tokken', $attribs, ','));

	$rootdir = weTag_getAttribute('rootdir', $attribs);
	$showpath = weTag_getAttribute('showpath', $attribs, false, true);
	$docAttr = weTag_getAttribute('doc', $attribs);
	$field = weTag_getAttribute('field', $attribs);
	$id = weTag_getAttribute('id', $attribs);
	$separator = weTag_getAttribute('separator', $attribs, '/');
	$onlyindir = weTag_getAttribute('onlyindir', $attribs);

	// end initialize possible Attributes
	if($id){
		$catIDs = $id;
	} elseif(isset($GLOBALS["lv"]) && (!$docAttr)){
		// get cats from listview object
		switch($GLOBALS["lv"]->ClassName){
			case "we_listview_object" :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
				break;
			case "we_search_listview" :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
				break;
			default :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
		}
	} else{
		$doc = we_getDocForTag($docAttr, false);
		$catIDs = $doc->Category;
	}
	$catIDs = implode(',', array_map('intval', explode(',', $catIDs)));
	$category = array_filter(we_getCatsFromIDs($catIDs, $delimiter, $showpath, $GLOBALS['DB_WE'], $rootdir, $field, $onlyindir, true));

	if(empty($category)){
		return '';
	}

	foreach($category as &$cat){
		$cat = str_replace('/', $separator, $cat);
	}
	return implode($delimiter, $category);
}