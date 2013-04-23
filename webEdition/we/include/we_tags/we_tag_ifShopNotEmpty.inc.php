<?php

/**
 * webEdition CMS
 *
 * $Rev: 5136 $
 * $Author: mokraemer $
 * $Date: 2012-11-13 00:10:47 +0100 (Tue, 13 Nov 2012) $
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
include_once(WE_MODULES_PATH . 'shop/we_conf_shop.inc.php');

/**
 * This functions checks if the shops basket is not empty
 *
 * @param          $attribs                                array
 *
 * @return         bool
 */
function we_tag_ifShopNotEmpty($attribs){
	$foo = attributFehltError($attribs, 'shopname', __FUNCTION__);

	if($foo){
		return $foo;
	}

	$shopname = weTag_getAttribute('shopname', $attribs);

	$basket = isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname] : '';

	if($basket){
		$shoppingItems = $basket->getShoppingItems();
		$basket_count = count($shoppingItems);

		return abs($basket_count) > 0;
	} else{
		return false;
	}
}
