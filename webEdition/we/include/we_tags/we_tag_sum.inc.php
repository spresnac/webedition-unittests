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
function we_tag_sum($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute("name", $attribs);
	$num_format = weTag_getAttribute("num_format", $attribs);
	$result = (isset($GLOBALS["summe"][$name]) ? we_util::std_numberformat($GLOBALS["summe"][$name]) : 0);

	switch($num_format){
		case "german":
			return number_format($result, 2, ",", ".");
		case "french":
			return number_format($result, 2, ",", " ");
		case "english":
			return number_format($result, 2, ".", "");
		case "swiss":
			return number_format($result, 2, ".", "'");
		default:
			return $result;
	}
}
