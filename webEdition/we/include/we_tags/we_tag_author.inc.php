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
function we_tag_author($attribs){
	// attributes
	$type = weTag_getAttribute("type", $attribs);
	$creator = weTag_getAttribute("creator", $attribs, false, true);
	$docAttr = weTag_getAttribute("doc", $attribs);

	$doc = we_getDocForTag($docAttr, true);

	$foo = getHash('SELECT Username,First,Second FROM ' . USER_TABLE . ' WHERE ID=' . intval($creator ? $doc->CreatorID : $doc->ModifierID), $GLOBALS['DB_WE']);

	switch($type){
		case "name" :
			$out = trim(($foo["First"] ? ($foo["First"] . " ") : "") . $foo["Second"]);
			if(!$out){
				$out = $foo["Username"];
			}
			return $out;
		case "initials" :
			$out = trim(
				($foo["First"] ? substr($foo["First"], 0, 1) : "") . ($foo["Second"] ? substr(
						$foo["Second"], 0, 1) : ""));
			if(!$out){
				$out = $foo["Username"];
			}
			return $out;
		default :
			return $foo["Username"];
	}
}
