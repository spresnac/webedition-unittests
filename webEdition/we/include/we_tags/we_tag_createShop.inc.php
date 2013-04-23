<?php

/**
 * webEdition CMS
 *
 * $Rev: 5572 $
 * $Author: mokraemer $
 * $Date: 2013-01-15 20:21:43 +0100 (Tue, 15 Jan 2013) $
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
function we_tag_createShop($attribs){
	if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__))){
		return $foo;
	}
	if(!defined("SHOP_TABLE")){
		return modulFehltError('Shop', __FUNCTION__);
	}

	$deleteshop = weTag_getAttribute("deleteshop", $attribs, false, true);
	$deleteshoponlogout = weTag_getAttribute("deleteshoponlogout", $attribs, false, true);
	$shopname = weTag_getAttribute("shopname", $attribs);

	include_once(WE_MODULES_PATH . 'shop/we_conf_shop.inc.php');

	if(!isset($_SESSION)){
		@session_start();
	}

	if(isset($_SESSION[$shopname . '_save']) && ((isset($_REQUEST["deleteshop"]) && $_REQUEST["deleteshop"] == 1) || $deleteshop)){ // delete shop
		unset($_SESSION[$shopname . '_save']);
	}
	if(isset($GLOBALS["WE_LOGOUT"]) && $GLOBALS["WE_LOGOUT"] && $deleteshoponlogout){
		unset($_SESSION[$shopname . '_save']);
	}

	$GLOBALS[$shopname] = new we_shop_Basket();
	$GLOBALS[$shopname]->setCartProperties((isset($_SESSION[$shopname . '_save']) ? $_SESSION[$shopname . '_save'] : array()));
	$GLOBALS[$shopname]->initCartFields();
	$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
}
