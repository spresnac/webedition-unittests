<?php

/**
 * webEdition CMS
 *
 * $Rev: 5576 $
 * $Author: mokraemer $
 * $Date: 2013-01-16 21:56:32 +0100 (Wed, 16 Jan 2013) $
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
function we_tag_ifSelf($attribs){

	$id = weTag_getAttribute("id", $attribs);

	if(!$id){
		if(isset($GLOBALS['we_obj'])){
			$id = $GLOBALS['we_obj']->ID;
		} else{
			$id = $GLOBALS["WE_MAIN_DOC"]->ID;
		}
	}
	$type = weTag_getAttribute("doc", $attribs);
	$type = $type ? $type : weTag_getAttribute("type", $attribs);

	$ids = makeArrayFromCSV($id);

	switch($type){
		case "listview" :
			if($GLOBALS["lv"]->ClassName == "we_listview_object"){
				return in_array($GLOBALS["lv"]->getDBf("OF_ID"), $ids);
			} else
			if($GLOBALS["lv"]->ClassName == "we_search_listview"){
				return in_array($GLOBALS["lv"]->getDBf("WE_ID"), $ids);
			} else
			if($GLOBALS["lv"]->ClassName == "we_shop_listviewShopVariants"){
				reset($GLOBALS['lv']->Record);
				$key = key($GLOBALS['lv']->Record);
				if(isset($GLOBALS['we_doc']->Variant)){

					if($key == $GLOBALS['we_doc']->Variant){
						return true;
					}
				} else{
					if($key == $GLOBALS['lv']->DefaultName){
						return true;
					}
				}
				return false;
			} else{
				return in_array($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1], $ids);
			}
		case "self" :
			if(isset($GLOBALS['we']['ll'])){
				return $GLOBALS['we']['ll']->getID()==$GLOBALS['we_doc']->ID;
			} else {
				return in_array($GLOBALS['we_doc']->ID, $ids);
			}
		default :
			if(isset($GLOBALS['we']['ll'])){
				return $GLOBALS['we']['ll']->getID()==$GLOBALS["WE_MAIN_DOC"]->ID;
			} else {
				return in_array($GLOBALS["WE_MAIN_DOC"]->ID, $ids);
			}
	}
}
