<?php

/**
 * webEdition CMS
 *
 * $Rev: 5051 $
 * $Author: mokraemer $
 * $Date: 2012-11-02 21:40:23 +0100 (Fri, 02 Nov 2012) $
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
function we_tag_navigationWrite($attribs){

	$name = weTag_getAttribute("navigationname", $attribs, "default");
	$depth = weTag_getAttribute("depth", $attribs);

	if(!$depth){
		$depth = false;
	}
	$ret = '';

	if(isset($GLOBALS['we_navigation'][$name])){

		$GLOBALS['weNavigationDepth'] = $depth;
		$ret = $GLOBALS['we_navigation'][$name]->writeNavigation($depth);
		unset($GLOBALS['weNavigationDepth']);
	}
	return $ret;
}
