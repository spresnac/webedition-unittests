<?php

/**
 * webEdition CMS
 *
 * $Rev: 4319 $
 * $Author: mokraemer $
 * $Date: 2012-03-22 19:22:48 +0100 (Thu, 22 Mar 2012) $
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


function getCustomersOrderList($customerId, $sameModul=true) {

	$weShopStatusMails = weShopStatusMails::getShopStatusMails();

	$orderStr = '
		<table class="defaultfont" width="1200">
	';

	// get orderdata of user here
	$da = ( $GLOBALS['WE_LANGUAGE'] == "Deutsch")?"%d.%m.%Y":"%m/%d/%Y";

	$query = '
		SELECT IntOrderID, DateOrder, DATE_FORMAT(DateOrder,"' . $da . '") AS formatDateOrder, DateConfirmation, DATE_FORMAT(DateConfirmation,"' . $da . '") AS formatDateConfirmation, DateCustomA, DATE_FORMAT(DateCustomA,"' . $da . '") AS formatDateCustomA, DateCustomB, DATE_FORMAT(DateCustomB,"' . $da . '") AS formatDateCustomB,DateCustomC, DATE_FORMAT(DateCustomC,"' . $da . '") AS formatDateCustomC,DateShipping, DATE_FORMAT(DateShipping,"' . $da . '") AS formatDateShipping, DateCustomD, DATE_FORMAT(DateCustomD,"' . $da . '") AS formatDateCustomD,DateCustomE, DATE_FORMAT(DateCustomE,"' . $da . '") AS formatDateCustomE, DatePayment, DATE_FORMAT(DatePayment,"' . $da . '") AS formatDatePayment,DateCustomF, DATE_FORMAT(DateCustomF,"' . $da . '") AS formatDateCustomF,DateCustomG, DATE_FORMAT(DateCustomG,"' . $da . '") AS formatDateCustomG,DateCancellation, DATE_FORMAT(DateCancellation,"' . $da . '") AS formatDateCancellation,DateCustomH, DATE_FORMAT(DateCustomH,"' . $da . '") AS formatDateCustomH,DateCustomI, DATE_FORMAT(DateCustomI,"' . $da . '") AS formatDateCustomI,DateCustomJ, DATE_FORMAT(DateCustomJ,"' . $da . '") AS formatDateCustomJ,DateFinished, DATE_FORMAT(DateFinished,"' . $da . '") AS formatDateFinished
		FROM ' . SHOP_TABLE . '
		WHERE IntCustomerID=' . intval($customerId) . '
		GROUP BY IntOrderId
		ORDER BY IntID DESC
	';

 	$GLOBALS['DB_WE']->query($query);

	if ($GLOBALS['DB_WE']->num_rows()) {

		$orderStr .='
		<tr>
			<td></td><td><b>' . g_l('modules_shop','[orderList][order]') . '</b></td>';

		if(!$weShopStatusMails->FieldsHidden['DateOrder']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateOrder'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateConfirmation']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateConfirmation'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomA']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomA'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomB']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomB'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomC']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomC'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateShipping']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateShipping'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomD']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomD'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomE']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomE'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DatePayment']){
			$orderStr .='			<td><b>' .  $weShopStatusMails->FieldsText['DatePayment'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomF']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomF'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomG']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomG'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCancellation']){
			$orderStr .='			<td><b>' .  $weShopStatusMails->FieldsText['DateCancellation'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomH']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomH'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomI']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomI'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateCustomF']){
			$orderStr .='			<td><b>' . $weShopStatusMails->FieldsText['DateCustomJ'] . '</b></td>';
		}
		if(!$weShopStatusMails->FieldsHidden['DateFinished']){
			$orderStr .='			<td><b>' .  $weShopStatusMails->FieldsText['DateFinished'] . '</b></td>';
		}

		$orderStr .='
		</tr>';

		while ($GLOBALS['DB_WE']->next_record()) {

			$orderStr .= '
		<tr>';
		if (we_hasPerm("EDIT_SHOP_ORDER")){
			$orderStr .=
			($sameModul ?
					('<td>' . we_button::create_button('image:btn_edit_edit', 'javascript:top.content.shop_properties.location = \'' . WE_SHOP_MODULE_DIR . 'edit_shop_editorFrameset.php?bid=' . $GLOBALS['DB_WE']->f('IntOrderID') . '\';' ) . '</td>') :
					('<td>' . we_button::create_button('image:btn_edit_edit', 'javascript:top.document.location = \'' . WE_MODULES_DIR . 'show_frameset.php?mod=shop&bid=' . $GLOBALS['DB_WE']->f('IntOrderID') . '\';' ) . '</td>')
              	);
		} else {
			$orderStr .='<td></td>';
		}
			$orderStr .= '<td>' . $GLOBALS['DB_WE']->f('IntOrderID') . '. ' . g_l('modules_shop','[orderList][order]') . '</td>';
			if(!$weShopStatusMails->FieldsHidden['DateOrder']){
				$orderStr .='<td>' . ( $GLOBALS['DB_WE']->f('DateOrder') > 0 ? $GLOBALS['DB_WE']->f('formatDateOrder') : '-'  ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateConfirmation']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateConfirmation') > 0  ? $GLOBALS['DB_WE']->f('formatDateConfirmation') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomA']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomA') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomA') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomB']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomB') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomB') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomC']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomC') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomC') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateShipping']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateShipping') > 0  ? $GLOBALS['DB_WE']->f('formatDateShipping') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomD']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomD') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomE') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomE']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomE') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomF') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DatePayment']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DatePayment') > 0  ? $GLOBALS['DB_WE']->f('formatDatePayment') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomF']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomF') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomF') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomG']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomG') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomG') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCancellation']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCancellation') > 0  ? $GLOBALS['DB_WE']->f('formatDateCancellation') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomH']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomH') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomH') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomI']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomI') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomI') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateCustomJ']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateCustomJ') > 0  ? $GLOBALS['DB_WE']->f('formatDateCustomJ') : '-' ) . '</td>';
			}
			if(!$weShopStatusMails->FieldsHidden['DateFinished']){
				$orderStr .= '<td>' . ( $GLOBALS['DB_WE']->f('DateFinished') > 0  ? $GLOBALS['DB_WE']->f('formaFinished') : '-' ) . '</td>';
			}

			$orderStr .=  '
		</tr>';
		}
	} else {
		$orderStr .= '
		<tr>
			<td>' . g_l('modules_shop','[orderList][noOrders]') . '</td>
		</tr>';
	}
	$orderStr .= '
		</table>
		';

	return $orderStr;
}

