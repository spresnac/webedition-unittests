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
function we_tag_ifDoctype($attribs){
	if(($foo = attributFehltError($attribs, "doctypes", __FUNCTION__))){
		print($foo);
		return false;
	}
	$match = weTag_getAttribute("doctypes", $attribs);

	$docAttr = weTag_getAttribute("doc", $attribs, "self");

	if($docAttr == "listview" && isset($GLOBALS['lv'])){
		$doctype = $GLOBALS['lv']->f('wedoc_DocType');
	} else{
		$doc = we_getDocForTag($docAttr);
		if($doc->ClassName == "we_template"){
			return false;
		}
		$doctype = $doc->DocType;
	}
	$matchArr = makeArrayFromCSV($match);

	if(isset($doctype) && $doctype != false){
		foreach($matchArr as $match){
			$matchID = f('SELECT ID FROM ' . DOC_TYPES_TABLE . " WHERE DocType='" . $GLOBALS['DB_WE']->escape($match) . "'", 'ID', $GLOBALS['DB_WE']);
			if($matchID == $doctype){
				return true;
			}
		}
	}
	return false;
}
