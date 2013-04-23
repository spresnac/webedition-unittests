<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
function we_tag_shopVat($attribs){

	$name = WE_SHOP_VAT_FIELD_NAME;


	$type = weTag_getAttribute('type', $attribs, 'select');
	$id = weTag_getAttribute('id', $attribs);


	if($id){

		$shopVat = weShopVats::getShopVATById($id);
		if($shopVat){
			return $shopVat->vat;
		}
	} else{


		// in webEdition - EditMode
		$allVats = weShopVats::getAllShopVATs();
		$values = array();

		$standardVal = '';

		foreach($allVats as $id => $shopVat){
			$values[$id] = $shopVat->vat . ' - ' . $shopVat->text;
			if($shopVat->standard){

				$standardId = $id;
				$standardVal = $shopVat->vat;
			}
		}

		$attribs['name'] = WE_SHOP_VAT_FIELD_NAME;
		$val = oldHtmlspecialchars(isset($GLOBALS['we_doc']->elements[$name]["dat"]) ? $GLOBALS['we_doc']->getElement($name) : $standardId);

		// use a defined name for this
		if($GLOBALS['we_editmode']){

			switch($type){
				default:
					$fieldname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
					return $GLOBALS['we_doc']->htmlSelect($fieldname, $values, 1, $val);
					break;
			}
		} else{
			return ( isset($allVats[$val]) ? $allVats[$val]->vat : $standardVal );
		}
	}
}
