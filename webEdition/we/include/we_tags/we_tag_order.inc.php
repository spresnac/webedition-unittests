<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_parse_tag_order($attribs, $content){
	return '<?php global $lv;
		if(' . we_tag_tagParser::printTag('order', $attribs) . '){?>' . $content . '<?php }
		we_post_tag_listview(); ?>';
}

function we_tag_order($attribs){
	if(!defined('WE_SHOP_MODULE_PATH')){
		print modulFehltError('Shop', __FUNCTION__);
		return false;
	}

	$condition = weTag_getAttribute("condition", $attribs, 0);
	$we_orderid = weTag_getAttribute("id", $attribs, 0);

	$hidedirindex = weTag_getAttribute("hidedirindex", $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);

	if(!isset($GLOBALS["we_lv_array"])){
		$GLOBALS["we_lv_array"] = array();
	}


	$we_orderid = $we_orderid ? $we_orderid : (isset($_REQUEST["we_orderid"]) ? $_REQUEST["we_orderid"] : 0);


	$GLOBALS["lv"] = new we_shop_ordertag(intval($we_orderid), $condition, $hidedirindex);
	if(is_array($GLOBALS["we_lv_array"])){
		$GLOBALS["we_lv_array"][] = clone($GLOBALS["lv"]);
	}
	return $GLOBALS["lv"]->avail;
}