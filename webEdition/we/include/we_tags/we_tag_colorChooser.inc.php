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
function we_tag_colorChooser($attribs){
	$foo = attributFehltError($attribs, "name", __FUNCTION__);
	if($foo)
		return $foo;

	if(!$GLOBALS['we_doc']->getElement($attribs["name"])){
		if(isset($attribs["value"]) && $attribs["value"])
			$GLOBALS['we_doc']->setElement($attribs["name"], $attribs["value"]);
	}

	if($GLOBALS['we_editmode']){
		$width = (isset($attribs["width"]) && $attribs["width"]) ? $attribs["width"] : 100;
		$height = (isset($attribs["height"]) && $attribs["height"]) ? $attribs["height"] : 18;
		return $GLOBALS['we_doc']->formColor($width, $attribs["name"], 25, "txt", $height);
	} else{
		return $GLOBALS['we_doc']->getElement($attribs["name"]);
	}
}
