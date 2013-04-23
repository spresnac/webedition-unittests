<?php

/**
 * webEdition CMS
 *
 * $Rev: 4489 $
 * $Author: mokraemer $
 * $Date: 2012-05-04 18:27:51 +0200 (Fri, 04 May 2012) $
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
function we_tag_ifField($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		print($foo);
		return "";
	}
	if(($foo = attributFehltError($attribs, "match", __FUNCTION__, true))){
		print($foo);
		return "";
	}
	if(($foo = attributFehltError($attribs, "type", __FUNCTION__, true))){
		print($foo);
		return "";
	}

	$match = weTag_getAttribute("match", $attribs);
//	$matchArray = makeArrayFromCSV($match);

	$operator = weTag_getAttribute("operator", $attribs);

	//Bug #4815
	if($attribs["type"] == 'float' || $attribs["type"] == 'int'){
		$attribs["type"] = 'text';
	}

	$realvalue = we_tag('field', $attribs);

	switch($operator){
		default:
		case "equal":
			return $realvalue == $match;
		case "less":
			return intval($realvalue) < intval($match);
		case "less|equal":
			return intval($realvalue) <= intval($match);
		case "greater":
			return intval($realvalue) > intval($match);
		case "greater|equal":
			return intval($realvalue) >= intval($match);
		case "contains":
			return (strpos($realvalue, $match) !== false);
	}
}
