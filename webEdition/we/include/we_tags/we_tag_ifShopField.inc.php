<?php

/**
 * webEdition CMS
 *
 * $Rev: 4243 $
 * $Author: mokraemer $
 * $Date: 2012-03-10 04:10:59 +0100 (Sat, 10 Mar 2012) $
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
function we_tag_ifShopField($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($attribs, "reference", __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($attribs, "match", __FUNCTION__, true)))
		return $foo;

	$match = weTag_getAttribute("match", $attribs);

	$name = weTag_getAttribute("name", $attribs);
	$reference = weTag_getAttribute("reference", $attribs);
	$shopname = weTag_getAttribute("shopname", $attribs);
	$operator = weTag_getAttribute("operator", $attribs);

	$atts = removeAttribs($attribs, array('match', 'operator'));
	$atts['type'] = 'print';
	$realvalue = we_tag('shopField', $atts);

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
