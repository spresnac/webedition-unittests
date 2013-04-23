<?php

/**
 * webEdition CMS
 *
 * $Rev: 5112 $
 * $Author: mokraemer $
 * $Date: 2012-11-09 20:02:22 +0100 (Fri, 09 Nov 2012) $
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
function we_parse_tag_metadata($attribs, $content){
	$arr = array();
	eval('$arr = ' . (PHPLOCALSCOPE ? str_replace('$', '\$', $attribs) : $attribs) . ';'); //Bug #6516
	if(($foo = attributFehltError($arr, 'name', __FUNCTION__)))
		return $foo;
	return '<?php if(' . we_tag_tagParser::printTag('metadata', $attribs) . '){?>' . $content . '<?php } we_post_tag_listview();?>';
}

function we_tag_metadata($attribs){
	$name = weTag_getAttribute("name", $attribs);

	if(!isset($GLOBALS["we_lv_array"])){
		$GLOBALS["we_lv_array"] = array();
	}

	$GLOBALS["lv"] = new metadatatag($name);
//$lv = clone($GLOBALS["lv"]); // for backwards compatibility
	if(is_array($GLOBALS["we_lv_array"]))
		array_push($GLOBALS["we_lv_array"], clone($GLOBALS["lv"]));

	return $GLOBALS["lv"]->avail;
}