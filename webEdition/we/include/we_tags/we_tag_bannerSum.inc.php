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
function we_tag_bannerSum($attribs){
	if(!isset($GLOBALS["lv"])){
		return false;
	}
	$foo = attributFehltError($attribs, "type", __FUNCTION__);
	if($foo)
		return $foo;
	$type = weTag_getAttribute("type", $attribs);
	switch($type){
		case "clicks":
			return $GLOBALS["lv"]->getAllclicks();
		case "views":
			return $GLOBALS["lv"]->getAllviews();
		case "rate":
			return $GLOBALS["lv"]->getAllrate();
	}
}
