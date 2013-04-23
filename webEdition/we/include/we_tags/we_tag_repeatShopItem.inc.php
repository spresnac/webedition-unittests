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
function we_parse_tag_repeatShopItem($attribs, $content){
	eval('$attribs = ' . $attribs . ';');
	if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__))){
		return $foo;
	}

	$attribs['_type'] = 'start';
	return '<?php ' . we_tag_tagParser::printTag('repeatShopItem', $attribs) . '; while($GLOBALS[\'lv\']->next_record()) {?>' . $content . '<?php } ' . we_tag_tagParser::printTag('repeatShopItem', array('_type' => 'stop')) . ';?>';
}

function we_tag_repeatShopItem($attribs){
	if(!defined("SHOP_TABLE")){
		print modulFehltError('Shop', __FUNCTION__);
		return;
	}
	$shopname = weTag_getAttribute("shopname", $attribs);

	//internal Attribute
	$_type = weTag_getAttribute('_type', $attribs);
	switch($_type){
		case 'start':
			if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__))){
				print $foo;
				return;
			}
			include_once(WE_MODULES_PATH . 'shop/we_conf_shop.inc.php');
			$_SESSION["we_shopname"] = $shopname;

			if(!isset($GLOBALS[$shopname]) || empty($GLOBALS[$shopname])){
				echo parseError(sprintf(g_l('parser', '[missing_createShop]'), 'repeatShopItem'));
			}
			$GLOBALS["lv"] = new we_shop_shop($GLOBALS[$shopname]);
			break;
		case 'stop':
			if(isset($GLOBALS['we_lv_array'])){
				we_post_tag_listview();
			} else{
				unset($GLOBALS["lv"]);
			}
			break;
	}
}
