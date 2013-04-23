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
function we_tag_options($attribs){
	$name = weTag_getAttribute("name", $attribs);
	$classid = weTag_getAttribute("classid", $attribs);
	$field = weTag_getAttribute("field", $attribs);

	$o = "";
	if($classid && $field){
		if(!isset($GLOBALS["WE_OBJECT_DEFARRAY"])){
			$GLOBALS["WE_OBJECT_DEFARRAY"] = array();
		}
		if(!isset($GLOBALS["WE_OBJECT_DEFARRAY"]["cid_$classid"])){
			$db = $GLOBALS['DB_WE'];
			$GLOBALS["WE_OBJECT_DEFARRAY"]["cid_$classid"] = unserialize(
				f("SELECT DefaultValues FROM " . OBJECT_TABLE . " WHERE ID='$classid'", "DefaultValues", $db));
		}
		$foo = $GLOBALS["WE_OBJECT_DEFARRAY"]["cid_$classid"]["meta_$field"]["meta"];
		foreach($foo as $key => $val){
			$o .= '<option value="' . $key . '"' . ((($GLOBALS[$name] == $key) && strlen($GLOBALS[$name]) != 0) ? " selected" : "") . '>' . $val . '</option>' . "\n";
		}
		return $o;
	}
	return '';
}
