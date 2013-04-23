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
function we_tag_objectLanguage($attribs){
	$type = weTag_getAttribute("type", $attribs, "complete");
	$case = weTag_getAttribute("case", $attribs, "unchanged");

	if(isset($GLOBALS['lv']) && isset($GLOBALS['lv']->object)){
		$lang = $GLOBALS['lv']->object->getDBf('OF_Language');
	} elseif(isset($GLOBALS['lv'])){
		$lang = $GLOBALS['lv']->getDBf('OF_Language');
	} else{
		$lang = '';
	}
	$out = "";
	switch($type){
		case "language":
			$out = substr($lang, 0, 2);
			break;
		case "country":
			$out = substr($lang, 3, 2);
			break;
		default:
			$out = $lang;
	}
	switch($case){
		case "uppercase":
			$out = strtoupper($out);
			break;
		case "lowercase":
			$out = strtolower($out);
			break;
		default:
			$out = $out;
	}
	return $out;
}
