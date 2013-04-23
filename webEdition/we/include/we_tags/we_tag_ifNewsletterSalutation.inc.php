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
function we_tag_ifNewsletterSalutation($attribs){
	if(($foo = attributFehltError($attribs, "type", __FUNCTION__, true))){
		print($foo);
		return false;
	}
	if(($foo = attributFehltError($attribs, "match", __FUNCTION__))){
		print($foo);
		return false;
	}
	$match = weTag_getParserAttribute("match", $attribs);
	$atts = removeAttribs($attribs, array('match'));
	return (we_tag('newsletterSalutation', $atts) == $match);
}
