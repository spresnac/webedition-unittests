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
include_once(WE_MODULES_PATH . 'shop/we_conf_shop.inc.php');

function we_tag_showShopItemNumber($attribs){

	if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__))){
		return $foo;
	}

	$shopname = weTag_getAttribute("shopname", $attribs);
	if(!isset($GLOBALS[$shopname]) || empty($GLOBALS[$shopname])){
		return parseError(sprintf(g_l('parser', '[missing_createShop]'), 'showShopItemNumber'));
	}
	$option = weTag_getAttribute("option", $attribs, false, true);
	$inputfield = weTag_getAttribute("inputfield", $attribs, false, true);
	$type = weTag_getAttribute("type", $attribs);

	$xml = weTag_getAttribute("xml", $attribs, false, true);
	$num_format = weTag_getAttribute("num_format", $attribs);
	$floatquantities = weTag_getAttribute("floatquantities", $attribs, false, true);
	$floatquantities = empty($floatquantities) ? false : $floatquantities;

	$attr = removeAttribs($attribs, array('option', 'inputfield', 'type', 'start', 'stop', 'shopname', 'nameto', 'to', 'floatquantities', '$num_format'));

	// $type of the field
	$articleType = 'w';

	if(isset($GLOBALS["lv"]->Record['OF_ID'])){
		$articleType = 'o';
	}

	if(isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->ShoppingCartKey)){
		$itemQuantity = $GLOBALS[$shopname]->Get_Item_Quantity($GLOBALS["lv"]->ShoppingCartKey);
	} else{
		$itemQuantity = 0;
	}


	if($option || ($type == "select")){

		$start = weTag_getAttribute("start", $attribs, 0);
		$stop = weTag_getAttribute("stop", $attribs, 10);
		$step = weTag_getAttribute("step", $attribs, 1);
		if($floatquantities){
			$stop = ( $stop > $itemQuantity ) ? $stop : $itemQuantity;
		} else{
			$stop = ( intval($stop) > intval($itemQuantity) ) ? $stop : $itemQuantity;
		}

		$out = '';


		$attr['name'] = 'shop_cart_id[' . $GLOBALS["lv"]->ShoppingCartKey . ']';

		$attr['size'] = 1;
		$attr['xml'] = $xml;

		while($start <= $stop) {
			if($itemQuantity == $start){
				$out .= getHtmlTag('option', array('xml' => $xml, 'value' => $start, 'selected' => 'selected'), $start);
			} else{
				$out .= getHtmlTag('option', array('xml' => $xml, 'value' => $start), $start);
			}
			$start = $start + $step;
		}
		return getHtmlTag('select', $attr, $out, true) . getHtmlTag('input', array('type' => 'hidden', 'name' => 't', 'value' => time()));
	} else if($inputfield || ($type == "textinput")){
		if($floatquantities){
			if($num_format == "german"){
				$itemQuantity = number_format($itemQuantity, 2, ",", ".");
			} else if($num_format == "french"){
				$itemQuantity = number_format($itemQuantity, 2, ",", " ");
			} else if($num_format == "english"){
				$itemQuantity = number_format($itemQuantity, 2, ".", "");
			} else if($num_format == "swiss"){
				$itemQuantity = number_format($itemQuantity, 2, ".", "'");
			}
		}
		$attr = array_merge($attr, array('type' => 'text', 'name' => 'shop_cart_id[' . $GLOBALS["lv"]->ShoppingCartKey . ']', 'size' => 2, 'value' => $itemQuantity));
		return getHtmlTag('input', $attr) . getHtmlTag('input', array('type' => 'hidden', 'name' => 't', 'value' => time()));
	} else{
		if($floatquantities){
			if($num_format == "german"){
				$itemQuantity = number_format($itemQuantity, 2, ",", ".");
			} else if($num_format == "french"){
				$itemQuantity = number_format($itemQuantity, 2, ",", " ");
			} else if($num_format == "english"){
				$itemQuantity = number_format($itemQuantity, 2, ".", "");
			} else if($num_format == "swiss"){
				$itemQuantity = number_format($itemQuantity, 2, ".", "'");
			}
		}
		return $itemQuantity;
	}
}
