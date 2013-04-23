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
function we_tag_ifVotingField($attribs){
	if(($foo = attributFehltError($attribs, "match", __FUNCTION__))){
		print($foo);
		return false;
	}

	$operator = weTag_getAttribute("operator", $attribs);
	$match = weTag_getAttribute("match", $attribs);

	$atts = removeAttribs($attribs, array('match', 'operator'));
	$realvalue = we_tag('votingField', $atts);

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
