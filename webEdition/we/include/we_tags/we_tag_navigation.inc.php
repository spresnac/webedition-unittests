<?php

/**
 * webEdition CMS
 *
 * $Rev: 4188 $
 * $Author: mokraemer $
 * $Date: 2012-03-04 14:48:38 +0100 (Sun, 04 Mar 2012) $
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
function we_tag_navigation($attribs){
	$parentid = weTag_getAttribute("parentid", $attribs, -1);
	$id = weTag_getAttribute("id", $attribs, 0);
	$name = weTag_getAttribute("navigationname", $attribs, "default");

	$GLOBALS['we_navigation'][$name] = new weNavigationItems();
	if(isset($GLOBALS['initNavigationFromSession']) && $GLOBALS['initNavigationFromSession']){
		$showRoot = ($parentid == -1);
		$GLOBALS['we_navigation'][$name]->initByNavigationObject($showRoot);
	} else{
		$realId = ($id ? $id : ($parentid != -1 ? $parentid : 0));
		if(!$GLOBALS['we_navigation'][$name]->initFromCache($realId, false)){
			//make sure we use cache next time!
			$showRoot = ($id ? true : ($parentid == -1));
			$GLOBALS['we_navigation'][$name]->initById($realId, false, $showRoot);
			weNavigationCache::saveCacheNavigation($realId, $GLOBALS['we_navigation'][$name]);
		}
	}
}
