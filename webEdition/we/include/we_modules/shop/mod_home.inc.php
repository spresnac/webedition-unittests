<?php

/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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
include_once(WE_MODULES_PATH . 'shop/handle_shop_dbitemConnect.php');


$prefshop = we_button::create_button("pref_shop", "javascript:top.opener.top.we_cmd('pref_shop');", true, -1, -1, "", "", !we_hasPerm("NEW_USER"));

$prefshop1 = we_button::create_button("payment_val", "javascript:top.opener.top.we_cmd('payment_val');", true, -1, -1, "", "", !we_hasPerm("NEW_USER"));
if(($resultD > 0) && (!empty($resultO))){ //docs and objects
	$prefshop2 = we_button::create_button("quick_rev", "javascript:top.content.shop_properties.location='" . WE_MODULES_DIR . "shop/edit_shop_editorFramesetTop.php?typ=document '", true);
} elseif(($resultD < 1) && (!empty($resultO))){ // no docs but objects
	$prefshop2 = we_button::create_button("quick_rev", "javascript:top.content.shop_properties.location='" . WE_MODULES_DIR . "shop/edit_shop_editorFramesetTop.php?typ=object&ViewClass=$classid '", true);
} elseif(($resultD > 0) && (empty($resultO))){ // docs but no objects
	$prefshop2 = we_button::create_button("quick_rev", "javascript:top.content.shop_properties.location='" . WE_MODULES_DIR . "shop/edit_shop_editorFramesetTop.php?typ=document '", true);
}

$content = $prefshop . we_html_tools::getPixel(2, 14) .
	$prefshop1 . we_html_tools::getPixel(2, 14) .
	(isset($prefshop2) ? $prefshop2 . we_html_tools::getPixel(2, 14) : '');


$modimage = "shop.gif";