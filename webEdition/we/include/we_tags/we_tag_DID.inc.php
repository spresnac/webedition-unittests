<?php

/**
 * webEdition CMS
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
function we_tag_DID($attribs){
	$docAttr = weTag_getAttribute("doc", $attribs);
	if(!$docAttr){
		$docAttr = weTag_getAttribute("type", $attribs); // for Compatibility Reasons
	}

	switch($docAttr){
		case "top" :
			return $GLOBALS["WE_MAIN_DOC"]->ID;
		case "listview" :
			return $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1];
		case "self" :
		default :
			return $GLOBALS['we_doc']->ID;
	}
}