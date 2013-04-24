<?php

/**
 * webEdition CMS
 *
 * $Rev: 4591 $
 * $Author: andreaswitt $
 * $Date: 2012-06-14 12:23:00 +0200 (Thu, 14 Jun 2012) $
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
function we_tag_ifShopFieldEmpty($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($attribs, "reference", __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__)))
		return $foo;

    $attribs['type'] = 'print'; //#6541

	return (we_tag('shopField', $attribs) == '');
}