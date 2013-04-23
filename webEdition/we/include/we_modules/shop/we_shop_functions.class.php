<?php

/**
 * webEdition CMS
 *
 * $Rev: 4955 $
 * $Author: mokraemer $
 * $Date: 2012-09-12 19:56:43 +0200 (Wed, 12 Sep 2012) $
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
class we_shop_functions{

	static function getCustomersOrderList($customerId, $sameModul = true){
		$weShopStatusMails = weShopStatusMails::getShopStatusMails();

		// get orderdata of user here
		$da = ( $GLOBALS['WE_LANGUAGE'] == 'Deutsch') ? '%d.%m.%Y' : '%m/%d/%Y';

		$format = array();
		foreach(weShopStatusMails::$StatusFields as $field){
			$format[] = 'DATE_FORMAT(' . $field . ',"' . $da . '") AS format' . $field;
		}
		$query = 'SELECT IntOrderID, ' . implode(',', weShopStatusMails::$StatusFields) . ', ' . implode(',', $format) . ' FROM ' . SHOP_TABLE . ' WHERE IntCustomerID=' . intval($customerId) . ' GROUP BY IntOrderId ORDER BY IntID DESC';

		$GLOBALS['DB_WE']->query($query);

		$orderStr = '<table class="defaultfont" width="1200">';
		if($GLOBALS['DB_WE']->num_rows()){
			$orderStr .='<tr>
			<td></td><td><b>' . g_l('modules_shop', '[orderList][order]') . '</b></td>';

			foreach(weShopStatusMails::$StatusFields as $field){
				if(!$weShopStatusMails->FieldsHidden[$field]){
					$orderStr .='<td><b>' . $weShopStatusMails->FieldsText[$field] . '</b></td>';
				}
			}

			$orderStr .='</tr>';

			while($GLOBALS['DB_WE']->next_record()) {

				$orderStr .= '<tr>';
				if(we_hasPerm("EDIT_SHOP_ORDER")){
					$orderStr .= ($sameModul ?
							('<td>' . we_button::create_button('image:btn_edit_edit', 'javascript:top.content.shop_properties.location = \'' . WE_SHOP_MODULE_DIR . 'edit_shop_editorFrameset.php?bid=' . $GLOBALS['DB_WE']->f('IntOrderID') . '\';') . '</td>') :
							('<td>' . we_button::create_button('image:btn_edit_edit', 'javascript:top.document.location = \'' . WE_MODULES_DIR . 'show_frameset.php?mod=shop&bid=' . $GLOBALS['DB_WE']->f('IntOrderID') . '\';') . '</td>')
						);
				} else{
					$orderStr .='<td></td>';
				}
				$orderStr .= '<td>' . $GLOBALS['DB_WE']->f('IntOrderID') . '. ' . g_l('modules_shop', '[orderList][order]') . '</td>';
				foreach(weShopStatusMails::$StatusFields as $field){
					if(!$weShopStatusMails->FieldsHidden[$field]){
						$orderStr .='<td>' . ( $GLOBALS['DB_WE']->f($field) > 0 ? $GLOBALS['DB_WE']->f('format' . $field) : '-' ) . '</td>';
					}
				}

				$orderStr .= '</tr>';
			}
		} else{
			$orderStr .= '<tr><td>' . g_l('modules_shop', '[orderList][noOrders]') . '</td></tr>';
		}
		$orderStr .= '</table>';

		return $orderStr;
	}

}