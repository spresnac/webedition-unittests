<?php

/**
 * webEdition CMS
 *
 * $Rev: 5976 $
 * $Author: mokraemer $
 * $Date: 2013-03-19 01:00:31 +0100 (Tue, 19 Mar 2013) $
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
function we_tag_navigationField($attribs){
	if(isset($GLOBALS['weNavigationItemArray']) && is_array($GLOBALS['weNavigationItemArray'])){

		$element = end($GLOBALS['weNavigationItemArray']);
		return $element->getNavigationField($attribs);
	}
	return '';
}