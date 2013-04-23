<?php
/**
 * webEdition CMS
 *
 * $Rev: 5873 $
 * $Author: mokraemer $
 * $Date: 2013-02-23 15:00:13 +0100 (Sat, 23 Feb 2013) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();
we_html_tools::htmlTop();
print STYLESHEET;

//$weShopVatRule = weShopVatRule::getShopVatRule();

$weShopStatusMails = weShopStatusMails::getShopStatusMails();

// Get Country and Lanfield Data
$strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $GLOBALS['DB_WE']);
if($strFelder !== ''){
	$CLFields = unserialize($strFelder);
} else{
	$CLFields['stateField'] = '-';
	$CLFields['stateFieldIsISO'] = 0;
	$CLFields['languageField'] = '-';
	$CLFields['languageFieldIsISO'] = 0;
}

function getFieldFromShoparticle(array $array, $name, $length = 0){
	$val = ( isset($array['we_' . $name]) ? $array['we_' . $name] : (isset($array[$name]) ? $array[$name] : '' ) );

	return ($length && ($length < strlen($val)) ?
			substr($val, 0, $length) . '...' :
			$val);
}

function getOrderCustomerData($orderId, $orderData = false, $customerId = false, $strFelder = array()){
	if(!$customerId){
		// get customerID from order
		$tmp = getHash('SELECT IntCustomerID, strSerialOrder FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($orderId), $GLOBALS['DB_WE']);

		if(!empty($tmp)){
			$customerId = $tmp['IntCustomerID'];
			$orderData = @unserialize($tmp['strSerialOrder']);
		}
	}

	// get Customer
	$customerDb = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($customerId), $GLOBALS['DB_WE']);
	$customerOrder = (isset($orderData[WE_SHOP_CART_CUSTOMER_FIELD]) ? $orderData[WE_SHOP_CART_CUSTOMER_FIELD] : array());

	// default values are fields saved with order
	$tmpCustomer = array_merge($customerDb, $customerOrder);

	// only fields explicity set with the order are shown here
	if(isset($strFelder) && isset($strFelder['customerFields'])){
		foreach($strFelder['customerFields'] as $k){
			if(isset($customerDb[$k])){
				$tmpCustomer[$k] = $customerDb[$k];
			}
		}
	}

	$_customer = array();

	foreach($tmpCustomer as $k => $v){
		if(!is_int($k)){
			$_customer[$k] = $v;
		}
	}
	return $_customer;
}

function getFieldFromOrder($bid, $field){
	return f('SELECT ' . $GLOBALS['DB_WE']->escape($field) . ' FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid), $field, $GLOBALS['DB_WE']);
}

function updateFieldFromOrder($orderId, $fieldname, $value){
	return (bool) $GLOBALS['DB_WE']->query('UPDATE ' . SHOP_TABLE . ' SET ' . $GLOBALS['DB_WE']->escape($fieldname) . '="' . $GLOBALS['DB_WE']->escape($value) . '" WHERE IntOrderID=' . intval($orderId));
}

// config
$feldnamen = explode('|', f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname = "shop_pref"', 'strFelder', $GLOBALS['DB_WE']));

$waehr = "&nbsp;" . oldHtmlspecialchars($feldnamen[0]);
$dbTitlename = "shoptitle";
$dbPreisname = "price";
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$classIds = makeArrayFromCSV($classid);
$mwst = (!empty($feldnamen[1])) ? (($feldnamen[1])) : '';
$notInc = "tblTemplates";

$da = "%d.%m.%Y";
$dateform = "00.00.0000";
$db = "%d.%m.%Y %H:%i";
$datetimeform = "00.00.0000 00:00";


if(isset($_REQUEST['we_cmd'][0])){
	switch($_REQUEST['we_cmd'][0]){
		case 'add_article':
			if(intval($_REQUEST["anzahl"]) > 0){

				// add complete article / object here - inclusive request fields
				$_strSerialOrder = getFieldFromOrder($_REQUEST["bid"], 'strSerialOrder');

				$tmp = explode('_', $_REQUEST['add_article']);
				$isObj = ($tmp[1] == 'o');

				$id = intval($tmp[0]);

				// check for variant or customfields
				$customFieldsTmp = array();
				if(strlen($_REQUEST['we_customField'])){

					$fields = explode(';', trim($_REQUEST['we_customField']));

					if(is_array($fields)){
						foreach($fields as $field){
							$fieldData = explode('=', $field);
							if(is_array($fieldData) && count($fieldData) == 2){
								$customFieldsTmp[trim($fieldData[0])] = trim($fieldData[1]);
							}
							unset($fieldData);
						}
					}
					unset($fields);
				}

				$variant = strip_tags($_REQUEST[WE_SHOP_VARIANT_REQUEST]);
				$serialDoc = we_shop_Basket::getserial($id, ($isObj ? 'o' : 'w'), $variant, $customFieldsTmp);

				unset($customFieldsTmp);

				// shop vats must be calculated
				$standardVat = weShopVats::getStandardShopVat();

				if(isset($serialDoc[WE_SHOP_VAT_FIELD_NAME])){
					$shopVat = weShopVats::getShopVATById($serialDoc[WE_SHOP_VAT_FIELD_NAME]);
				}

				if(isset($shopVat) && $shopVat){
					$serialDoc[WE_SHOP_VAT_FIELD_NAME] = $shopVat->vat;
				} elseif($standardVat){
					$serialDoc[WE_SHOP_VAT_FIELD_NAME] = $standardVat->vat;
				}

				//need pricefield:
				$orderArray=  unserialize($_strSerialOrder);
				$pricename = (isset($orderArray[WE_SHOP_PRICENAME]) ? $orderArray[WE_SHOP_PRICENAME] : 'shopprice');
				// now insert article to order:
				$row = getHash('SELECT IntOrderID, IntCustomerID, DateOrder, DateShipping, Datepayment, IntPayment_Type FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($_REQUEST['bid']), $GLOBALS['DB_WE']);
				$GLOBALS['DB_WE']->query('INSERT INTO ' . SHOP_TABLE . ' SET ' .
					we_database_base::arraySetter((array(
						'IntArticleID' => $id,
						'IntQuantity' => $_REQUEST["anzahl"],
						'Price' => we_util::std_numberformat(getFieldFromShoparticle($serialDoc, $pricename)),
						'IntOrderID' => $row['IntOrderID'],
						'IntCustomerID' => $row['IntCustomerID'],
						'DateOrder' => $row['DateOrder'],
						'DateShipping' => $row['DateShipping'],
						'Datepayment' => $row['Datepayment'],
						'IntPayment_Type' => $row['IntPayment_Type'],
						'strSerial' => serialize($serialDoc),
						'strSerialOrder' => $_strSerialOrder
				))));
			} else{
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true));
			}

			break;

		case 'add_new_article':
			$shopArticles = array();

			$saveBut = '';
			$cancelBut = we_button::create_button('cancel', 'javascript:window.close();');
			$searchBut = we_button::create_button('search', 'javascript:searchArticles();');

			// first get all shop documents
			$GLOBALS['DB_WE']->query('SELECT ' . CONTENT_TABLE . '.dat AS shopTitle, ' . LINK_TABLE . '.DID AS documentId FROM ' . CONTENT_TABLE . ', ' . LINK_TABLE . ', ' . FILE_TABLE .
				' WHERE ' . FILE_TABLE . '.ID = ' . LINK_TABLE . '.DID
					AND ' . LINK_TABLE . '.CID = ' . CONTENT_TABLE . '.ID
					AND ' . LINK_TABLE . '.Name = "shoptitle"
					AND ' . LINK_TABLE . '.DocumentTable != "tblTemplates" ' .
				(isset($_REQUEST['searchArticle']) && $_REQUEST['searchArticle'] ?
					' AND ' . CONTENT_TABLE . '.Dat LIKE "%' . $GLOBALS['DB_WE']->escape($_REQUEST['searchArticle']) . '%"' :
					'')
			);

			while($GLOBALS['DB_WE']->next_record()) {
				$shopArticles[$GLOBALS['DB_WE']->f('documentId') . '_d'] = $GLOBALS['DB_WE']->f("shopTitle") . ' [' . $GLOBALS['DB_WE']->f("documentId") . ']' . g_l('modules_shop', '[isDoc]');
			}

			if(defined('OBJECT_TABLE')){
				// now get all shop objects
				foreach($classIds as $_classId){
					$_classId = intval($_classId);
					$GLOBALS['DB_WE']->query('SELECT  ' . OBJECT_X_TABLE . $_classId . '.input_shoptitle as shopTitle, ' . OBJECT_X_TABLE . $_classId . '.OF_ID as objectId
						FROM ' . OBJECT_X_TABLE . $_classId . ', ' . OBJECT_FILES_TABLE . '
						WHERE ' . OBJECT_X_TABLE . $_classId . '.OF_ID = ' . OBJECT_FILES_TABLE . '.ID
							AND ' . OBJECT_X_TABLE . $_classId . '.ID = ' . OBJECT_FILES_TABLE . '.ObjectID ' .
						(isset($_REQUEST['searchArticle']) && $_REQUEST['searchArticle'] ?
							' AND ' . OBJECT_X_TABLE . $_classId . '.input_shoptitle  LIKE "%' . $GLOBALS['DB_WE']->escape($_REQUEST['searchArticle']) . '%"' :
							'')
					);

					while($GLOBALS['DB_WE']->next_record()) {
						$shopArticles[$GLOBALS['DB_WE']->f('objectId') . '_o'] = $GLOBALS['DB_WE']->f('shopTitle') . ' [' . $GLOBALS['DB_WE']->f('objectId') . ']' . g_l('modules_shop', '[isObj]');
					}
				}
				unset($_classId);
			}

			// <<< determine which articles should be shown ...

			asort($shopArticles);
			$MAX_PER_PAGE = 15;
			$AMOUNT_ARTICLES = count($shopArticles);

			$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;

			$shopArticlesParts = array_chunk($shopArticles, $MAX_PER_PAGE, true);

			$start_entry = $page * $MAX_PER_PAGE + 1;
			$end_entry = (($page * $MAX_PER_PAGE + $MAX_PER_PAGE < $AMOUNT_ARTICLES) ? ($page * $MAX_PER_PAGE + $MAX_PER_PAGE) : $AMOUNT_ARTICLES );

			$backBut = ($start_entry - $MAX_PER_PAGE > 0 ?
					we_button::create_button('back', "javascript:switchEntriesPage(" . ($page - 1) . ");") :
					we_button::create_button('back', "#", true, 100, 22, '', '', true));

			$nextBut = (($end_entry) < $AMOUNT_ARTICLES ?
					we_button::create_button('next', "javascript:switchEntriesPage(" . ($page + 1) . ");") :
					we_button::create_button('next', "#", true, 100, 22, '', '', true));


			$shopArticlesSelect = $shopArticlesParts[$page];
			asort($shopArticlesSelect);

			// determine which articles should be shown >>>


			print we_html_element::jsElement('
self.focus();

function selectArticle(articleInfo) {
	document.location = "?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&bid=' . $_REQUEST['bid'] . '&page=' . $page . (isset($_REQUEST['searchArticle']) ? '&searchArticle=' . $_REQUEST['searchArticle'] : '') . '&add_article=" + articleInfo;
}

function switchEntriesPage(pageNum) {
	document.location = "?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&bid=' . $_REQUEST['bid'] . (isset($_REQUEST['searchArticle']) ? '&searchArticle=' . $_REQUEST['searchArticle'] : '') . '&page=" + pageNum;
}

function searchArticles() {
	field = document.getElementById("searchArticle");
	document.location = "?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&bid=' . $_REQUEST['bid'] . '&searchArticle=" + field.value;
}') . '
</head>
<body class="weDialogBody">';

			$parts = array(($AMOUNT_ARTICLES > 0 ?
					array(
					'headline' => g_l('modules_shop', '[Artikel]'),
					'space' => 100,
					'html' => '
<form name="we_intern_form">' . we_html_tools::hidden('bid', $_REQUEST['bid']) . we_html_tools::hidden("we_cmd[]", 'add_new_article') . '
	<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . we_class::htmlSelect("add_article", $shopArticlesSelect, 15, (isset($_REQUEST['add_article']) ? $_REQUEST['add_article'] : ''), false, 'onchange="selectArticle(this.options[this.selectedIndex].value);"', 'value', '380') . '</td>
	<td>' . we_html_tools::getPixel(10, 1) . '</td>
	<td valign="top">' . $backBut . '<div style="margin:5px 0"></div>' . $nextBut . '</td>
	</tr>
	<tr>
		<td class="small">' . sprintf(g_l('modules_shop', '[add_article][entry_x_to_y_from_z]'), $start_entry, $end_entry, $AMOUNT_ARTICLES) . '</td>
	</tr>
	</table>',
					'noline' => 1
					) :
					array(
					'headline' => g_l('modules_shop', '[Artikel]'),
					'space' => 100,
					'html' => g_l('modules_shop', '[add_article][empty_articles]')
					)
				)
			);

			if($AMOUNT_ARTICLES > 0 || isset($_REQUEST['searchArticle'])){
				$parts[] = array(
					'headline' => g_l('global', '[search]'),
					'space' => 100,
					'html' => '
	<table border="0" cellpadding="0" cellspacing="0">
		<tr><td>' . we_class::htmlTextInput('searchArticle', 24, ( isset($_REQUEST['searchArticle']) ? $_REQUEST['searchArticle'] : ''), '', 'id="searchArticle"', 'text', 380) . '</td>
			<td>' . we_html_tools::getPixel(10, 1) . '</td>
			<td>' . $searchBut . '</td>
		</tr>
	</table>
</form>'
				);
			}

			if(isset($_REQUEST['add_article']) && $_REQUEST['add_article'] != '0'){
				$saveBut = we_button::create_button('save', "javascript:document.we_form.submit();window.close();");
				list($id, $type) = explode('_', $_REQUEST['add_article']);

				$variantOptions = array(
					'-' => '-'
				);

				if($type == 'o'){
					$model = new we_objectFile();
					$model->initByID($id, OBJECT_FILES_TABLE);

					$variantData = weShopVariants::getVariantData($model, '-');
				} else{
					$model = new we_webEditionDocument();
					$model->initByID($id);

					$variantData = weShopVariants::getVariantData($model, '-');
				}

				if(count($variantData) > 1){
					foreach($variantData as $cur){
						list($key, $varData) = each($cur);
						if($key != '-'){
							$variantOptions[$key] = $key;
						}
					}
				}

				$parts[] = array(
					'headline' => g_l('modules_shop', '[Artikel]'),
					'space' => 100,
					'html' => '
					<form name="we_form" target="edbody">
					' . we_html_tools::hidden('bid', $_REQUEST['bid']) .
					we_html_tools::hidden('we_cmd[]', 'add_article') .
					we_html_tools::hidden('add_article', $_REQUEST['add_article']) .
					'
					<b>' . $model->elements['shoptitle']['dat'] . '</b>',
					'noline' => 1
				);

				unset($model);

				$parts[] = array(
					'headline' => g_l('modules_shop', '[anzahl]'),
					'space' => 100,
					'html' => we_class::htmlTextInput('anzahl', 24, '', '', 'min="1"', 'number', 380),
					'noline' => 1
				);

				$parts[] = array(
					'headline' => g_l('modules_shop', '[variant]'),
					'space' => 100,
					'html' => we_class::htmlSelect(WE_SHOP_VARIANT_REQUEST, $variantOptions, 1, '', false, '', 'value', 380),
					'noline' => 1
				);

				$parts[] = array(
					'headline' => g_l('modules_shop', '[customField]'),
					'space' => 100,
					'html' => we_class::htmlTextInput('we_customField', 24, '', '', '', 'text', 380) .
					'<br /><span class="small">Eingabe in der Form: <i>name1=wert1;name2=wert2</i></span></form>',
					'noline' => 1
				);

				unset($id);
				unset($type);
				unset($variantData);
				unset($model);
			}


			print we_multiIconBox::getHTML('', '100%', $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_article][title]')) .
				'</form>
</body>
</html>';
			exit;
			break;

		case 'payVat':

			$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

			$serialOrder = @unserialize($strSerialOrder);
			$serialOrder[WE_SHOP_CALC_VAT] = $_REQUEST['pay'] == '1' ? 1 : 0;

			// update all orders with this orderId
			if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_success]');
				$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
			} else{
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_error]');
				$alertType = we_message_reporting::WE_MESSAGE_ERROR;
			}
			unset($serialOrder);
			unset($strSerialOrder);
			break;

		case 'delete_shop_cart_custom_field':

			if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

				$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				$serialOrder = @unserialize($strSerialOrder);
				unset($serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']]);

				// update all orders with this orderId
				if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_success]'), $_REQUEST['cartfieldname']);
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else{
					$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_error]'), $_REQUEST['cartfieldname']);
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
			}
			unset($strSerialOrder);
			unset($serialOrder);
			break;

		case 'edit_shop_cart_custom_field':

			print we_html_element::jsElement('function we_submit() {
		elem = document.getElementById("cartfieldname");

		if (elem && elem.value) {
			document.we_form.submit();
		} else {
			' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		}

	}') . '
			</head>
<body class="weDialogBody">
<form name="we_form">
<input type="hidden" name="bid" value="' . $_REQUEST['bid'] . '" />
<input type="hidden" name="we_cmd[0]" value="save_shop_cart_custom_field" />';
			$saveBut = we_button::create_button('save', "javascript:we_submit();");
			$cancelBut = we_button::create_button('cancel', "javascript:self.close();");


			$val = '';

			if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

				$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
				$serialOrder = @unserialize($strSerialOrder);

				$val = $serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] ? $serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] : '';

				$fieldHtml = $_REQUEST['cartfieldname'] . '<input type="hidden" name="cartfieldname" id="cartfieldname" value="' . $_REQUEST['cartfieldname'] . '" />';
			} else{
				$fieldHtml = we_html_tools::htmlTextInput('cartfieldname', 24, '', '', 'id="cartfieldname"');
			}

			// make input field, for name or textfield
			$parts = array(
				array(
					'headline' => g_l('modules_shop', '[field_name]'),
					'html' => $fieldHtml,
					'space' => 120,
					'noline' => 1
				),
				array(
					'headline' => g_l('modules_shop', '[field_value]'),
					'html' => '<textarea name="cartfieldvalue" style="width: 350; height: 150">' . $val . '</textarea>',
					'space' => 120
				)
			);

			print we_multiIconBox::getHTML('', '100%', $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_shop_field]'));
			unset($saveBut);
			unset($cancelBut);
			unset($parts);
			unset($val);
			unset($fieldHtml);
			print '
				</form></body>
</html>';
			exit;

		case 'save_shop_cart_custom_field':

			if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

				$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				$serialOrder = @unserialize($strSerialOrder);
				$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = htmlentities($_REQUEST['cartfieldvalue']);
				$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = $_REQUEST['cartfieldvalue'];

				// update all orders with this orderId
				if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$jsCmd = '
					top.opener.top.content.shop_tree.doClick(' . $_REQUEST['bid'] . ',"shop","' . SHOP_TABLE . '");' .
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_success]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_NOTICE) .
						'window.close();';
				} else{
					$jsCmd = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_error]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_ERROR) .
						'window.close();';
				}
			} else{

				$jsCmd = we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR) .
					'window.close();';
			}

			print we_html_element::jsElement($jsCmd) .
				'</head>
<body></body>
</html>';
			unset($serialOrder);
			unset($strSerialOrder);
			exit;

		case 'edit_shipping_cost':
			$shopVats = weShopVats::getAllShopVATs();
			$shippingVats = array();

			foreach($shopVats as $k => $shopVat){
				if(strlen($shopVat->vat . ' - ' . $shopVat->text) > 20){
					$shippingVats[$shopVat->vat] = substr($shopVat->vat . ' - ' . $shopVat->text, 0, 16) . ' ...';
				} else{
					$shippingVats[$shopVat->vat] = $shopVat->vat . ' - ' . $shopVat->text;
				}
			}

			unset($shopVat);
			unset($shopVats);
			$saveBut = we_button::create_button('save', "javascript:document.we_form.submit();self.close();");
			$cancelBut = we_button::create_button('cancel', "javascript:self.close();");

			$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

			if($strSerialOrder){

				$serialOrder = @unserialize($strSerialOrder);

				$shippingCost = $serialOrder[WE_SHOP_SHIPPING]['costs'];
				$shippingIsNet = $serialOrder[WE_SHOP_SHIPPING]['isNet'];
				$shippingVat = $serialOrder[WE_SHOP_SHIPPING]['vatRate'];
			} else{

				$shippingCost = '0';
				$shippingIsNet = '1';
				$shippingVat = '19';
			}

			$parts = array(
				array(
					'headline' => g_l('modules_shop', '[edit_order][shipping_costs]'),
					'space' => 150,
					'html' => we_class::htmlTextInput("weShipping_costs", 24, $shippingCost),
					'noline' => 1
				),
				array(
					'headline' => g_l('modules_shop', '[edit_shipping_cost][isNet]'),
					'space' => 150,
					'html' => we_class::htmlSelect("weShipping_isNet", array('1' => g_l('global', '[yes]'), '0' => g_l('global', '[no]')), 1, $shippingIsNet),
					'noline' => 1
				),
				array(
					'headline' => g_l('modules_shop', '[edit_shipping_cost][vatRate]'),
					'space' => 150,
					'html' => we_html_tools::htmlInputChoiceField("weShipping_vatRate", $shippingVat, $shippingVats, array(), '', true),
					'noline' => 1
				)
			);


			print '
				</head>
				<body class="weDialogBody">
				<form name="we_form" target="edbody">
				' . we_html_tools::hidden('bid', $_REQUEST['bid']) .
				we_html_tools::hidden("we_cmd[]", 'save_shipping_cost') .
				we_multiIconBox::getHTML("", "100%", $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, "", "", false, g_l('modules_shop', '[edit_shipping_cost][title]')) .
				'</form>
				</body>
				</html>';
			exit;
			break;

		case 'save_shipping_cost':

			$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
			$serialOrder = @unserialize($strSerialOrder);

			if($serialOrder){

				$weShippingCosts = str_replace(",", ".", $_REQUEST['weShipping_costs']);
				$serialOrder[WE_SHOP_SHIPPING]['costs'] = $weShippingCosts;
				$serialOrder[WE_SHOP_SHIPPING]['isNet'] = $_REQUEST['weShipping_isNet'];
				$serialOrder[WE_SHOP_SHIPPING]['vatRate'] = $_REQUEST['weShipping_vatRate'];

				// update all orders with this orderId
				if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_success]');
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else{
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_error]');
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
			}

			unset($strSerialOrder);
			unset($serialOrder);
			break;

		case 'edit_order_customer'; // edit data of the saved customer.
			$saveBut = we_button::create_button('save', "javascript:document.we_form.submit();self.close();");
			$cancelBut = we_button::create_button('cancel', "javascript:self.close();");
			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}
			// 1st get the customer for this order
			$_customer = getOrderCustomerData($_REQUEST['bid']);
			ksort($_customer);

			$dontEdit = array('ID', 'Username', 'Password', 'MemberSince', 'LastLogin', 'LastAccess', 'ParentID', 'Path', 'IsFolder', 'Icon', 'Text', 'Forename', 'Surname');

			$parts = array(
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[preferences][explanation_customer_odercustomer]'), 2, 470),
					'space' => 0
				),
				array(
					'headline' => g_l('modules_customer', '[Forname]') . ": ",
					'space' => 150,
					'html' => we_class::htmlTextInput("weCustomerOrder[Forename]", 44, $_customer['Forename']),
					'noline' => 1
				),
				array(
					'headline' => g_l('modules_customer', '[Surname]') . ": ",
					'space' => 150,
					'html' => we_class::htmlTextInput("weCustomerOrder[Surname]", 44, $_customer['Surname']),
					'noline' => 1
				)
			);
			$editFields = array('Forename', 'Surname');

			foreach($_customer as $k => $v){
				if(!in_array($k, $dontEdit)){
					if(isset($CLFields['stateField']) && isset($CLFields['stateFieldIsISO']) && $k == $CLFields['stateField'] && $CLFields['stateFieldIsISO']){
						$lang = explode('_', $GLOBALS["WE_LANGUAGE"]);
						$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
						$countrycode = array_search($langcode, $GLOBALS['WE_LANGS_COUNTRIES']);
						$countryselect = new we_html_select(array("name" => "weCustomerOrder[$k]", "size" => "1", "style" => "{width:280;}", "class" => "wetextinput"));

						$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
						if(!Zend_Locale::hasCache()){
							Zend_Locale::setCache(getWEZendCache());
						}
						foreach($topCountries as $countrykey => &$countryvalue){
							$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
						}
						unset($countryvalue);
						$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
						foreach($shownCountries as $countrykey => &$countryvalue){
							$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
						}
						unset($countryvalue);
						$oldLocale = setlocale(LC_ALL, NULL);
						setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
						asort($topCountries, SORT_LOCALE_STRING);
						asort($shownCountries, SORT_LOCALE_STRING);
						setlocale(LC_ALL, $oldLocale);

						$content = '';
						if(WE_COUNTRIES_DEFAULT != ''){
							$countryselect->addOption('--', CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT));
						}
						foreach($topCountries as $countrykey => &$countryvalue){
							$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
						}
						unset($countryvalue);
						$countryselect->addOption('-', '----', array("disabled" => "disabled"));
						//$content.='<option value="-" disabled="disabled">----</option>'."\n";
						foreach($shownCountries as $countrykey => &$countryvalue){
							$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
						}
						unset($countryvalue);
						$countryselect->selectOption($v);

						$parts[] = array(
							'headline' => "$k: ",
							'space' => 150,
							'html' => $countryselect->getHtml(),
							'noline' => 1
						);
					} elseif((isset($CLFields['languageField']) && isset($CLFields['languageFieldIsISO']) && $k == $CLFields['languageField'] && $CLFields['languageFieldIsISO'])){
						$frontendL = $GLOBALS["weFrontendLanguages"];
						foreach($frontendL as $lc => &$lcvalue){
							$lccode = explode('_', $lcvalue);
							$lcvalue = $lccode[0];
						}
						unset($countryvalue);
						$languageselect = new we_html_select(array("name" => "weCustomerOrder[$k]", "size" => "1", "style" => "{width:280;}", "class" => "wetextinput"));
						foreach(g_l('languages', '') as $languagekey => $languagevalue){
							if(in_array($languagekey, $frontendL)){
								$languageselect->addOption($languagekey, $languagevalue);
							}
						}
						$languageselect->selectOption($v);

						$parts[] = array(
							'headline' => "$k: ",
							'space' => 150,
							'html' => $languageselect->getHtml(),
							'noline' => 1
						);
					} else{
						$parts[] = array(
							'headline' => "$k: ",
							'space' => 150,
							'html' => we_class::htmlTextInput("weCustomerOrder[$k]", 44, $v),
							'noline' => 1
						);
					}
					$editFields[] = $k;
				}
			}

			print '</head>
				<body class="weDialogBody">
				<form name="we_form" target="edbody">' .
				we_html_tools::hidden('bid', $_REQUEST['bid']) .
				we_html_tools::hidden("we_cmd[]", 'save_order_customer') .
				we_multiIconBox::getHTML("", "100%", $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, "", "", false, g_l('modules_shop', '[preferences][customerdata]'), "", 560) .
				'</form>
				</body>
				</html>';
			exit;

		case 'save_order_customer':

			// just get this order and save this userdata in there.

			$_strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

			$_orderData = @unserialize($_strSerialOrder);
			$_customer = $_REQUEST['weCustomerOrder'];

			$_orderData[WE_SHOP_CART_CUSTOMER_FIELD] = $_customer;


			if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($_orderData))){
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_success]');
				$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
			} else{
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_error]');
				$alertType = we_message_reporting::WE_MESSAGE_ERROR;
			}

			unset($upQuery);
			unset($_customer);
			unset($_orderData);
			unset($_strSerialOrder);
			break;
	}
}

if(isset($_REQUEST["deletethisorder"])){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . SHOP_TABLE . ' WHERE IntOrderID = ' . $_REQUEST['bid']);
	echo we_html_element::jsElement('top.content.deleteEntry(' . $_REQUEST['bid'] . ')') .
	'</head>
	<body class="weEditorBody" onunload="doUnload()">
	<table border="0" cellpadding="0" cellspacing="2" width="300">
      <tr>
        <td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout("<span class='defaultfont'>" . g_l('modules_shop', '[geloscht]') . "</span>", g_l('modules_shop', '[loscht]')) . '</td>
      </tr>
      </table></html>';
	exit;
}

if(isset($_REQUEST["deleteaartikle"])){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . SHOP_TABLE . ' WHERE IntID = ' . $_REQUEST["deleteaartikle"]);
	$l = f('SELECT COUNT(1) AS a FROM ' . SHOP_TABLE . ' WHERE IntOrderID = ' . intval($_REQUEST["bid"]), 'a', $GLOBALS['DB_WE']);
	if($l < 1){
		$letzerartikel = 1;
	}
}
// Get Customer data
$_REQUEST["cid"] = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . '	WHERE IntOrderID = ' . intval($_REQUEST["bid"]), 'IntCustomerID', $GLOBALS['DB_WE']);

$strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname = "edit_shop_properties"', 'strFelder', $GLOBALS['DB_WE']);

if(($fields = @unserialize($strFelder))){
	// we have an array with following syntax:
	// array ( 'customerFields' => array('fieldname ...',...)
	//         'orderCustomerFields' => array('fieldname', ...) )
} else{

	$fields['customerFields'] = array();
	$fields['orderCustomerFields'] = array();

	// the save format used to be ...
	// Vorname:tblWebUser||Forename,Nachname:tblWebUser||Surname,Contact/Address1:tblWebUser||Contact_Address1,Contact/Address1:tblWebUser||Contact_Address1,...
	$_fieldInfos = explode(',', $strFelder);

	foreach($_fieldInfos as $_fieldInfo){

		$tmp1 = explode('||', $_fieldInfo);
		$tmp2 = explode(':', $tmp1[0]);

		$_fieldname = $tmp1[1];
		$_titel = $tmp2[0];
		$_tbl = $tmp2[1];

		if($_tbl != 'webE'){
			$fields['customerFields'][] = $_fieldname;
		}
	}
	$fields['customerFields'] = array_unique($fields['customerFields']);

	unset($_tmpEntries);
}

// >>>> Getting customer data
//$_customer = getOrderCustomerData(0, $orderData, $_REQUEST['cid'], $fields);
$_customer = getOrderCustomerData(0, 0, $_REQUEST['cid'], $fields);
// <<<< End of getting customer data




if(isset($_REQUEST["SendMail"])){
	$weShopStatusMails->sendEMail($_REQUEST["SendMail"], $_REQUEST["bid"], $_customer);
}
foreach(weShopStatusMails::$StatusFields as $field){
	if(isset($_REQUEST[$field])){
		list($day, $month, $year) = explode('.', $_REQUEST[$field]);
		$DateOrder = $year . "-" . $month . "-" . $day . " 00:00:00";
		$GLOBALS['DB_WE']->query('UPDATE ' . SHOP_TABLE . ' SET ' . $field . '="' . $GLOBALS['DB_WE']->escape($DateOrder) . '" WHERE IntOrderID = ' . intval($_REQUEST["bid"]));
		$weShopStatusMails->checkAutoMailAndSend(substr($field, 4), $_REQUEST["bid"], $_customer);
	}
}

if(isset($_REQUEST["article"])){
	if(isset($_REQUEST["preis"])){
		$GLOBALS['DB_WE']->query('UPDATE ' . SHOP_TABLE . ' SET Price=' . abs($_REQUEST["preis"]) . ' WHERE IntID = ' . intval($_REQUEST["article"]));
	} else if(isset($_REQUEST["anzahl"])){
		$GLOBALS['DB_WE']->query('UPDATE ' . SHOP_TABLE . ' SET IntQuantity=' . abs($_REQUEST["anzahl"]) . ' WHERE IntID = ' . intval($_REQUEST["article"]));
	} else if(isset($_REQUEST['vat'])){

		$GLOBALS['DB_WE']->query('SELECT strSerial FROM ' . SHOP_TABLE . ' WHERE IntID = ' . $GLOBALS['DB_WE']->escape($_REQUEST["article"]));

		if($GLOBALS['DB_WE']->num_rows() == 1){
			$GLOBALS['DB_WE']->next_record();

			$strSerial = $GLOBALS['DB_WE']->f('strSerial');
			$tmpDoc = @unserialize($strSerial);
			$tmpDoc[WE_SHOP_VAT_FIELD_NAME] = $_REQUEST['vat'];

			$GLOBALS['DB_WE']->query('UPDATE ' . SHOP_TABLE . ' SET strSerial="' . $GLOBALS['DB_WE']->escape(serialize($tmpDoc)) . '" WHERE IntID = ' . intval($_REQUEST['article']));
			unset($strSerial);
			unset($tmpDoc);
		}
	}
}

if(!isset($letzerartikel)){ // order has still articles - get them all
	// ********************************************************************************
	// first get all information about orders, we need this for the rest of the page
	//

	$format = array();
	foreach(weShopStatusMails::$StatusFields as $field){
		$format[] = 'DATE_FORMAT(' . $field . ',"' . $da . '") AS ' . $field;
	}
	foreach(weShopStatusMails::$MailFields as $field){
		$format[] = 'DATE_FORMAT(' . $field . ',"' . $db . '") AS ' . $field;
	}

	$GLOBALS['DB_WE']->query('SELECT IntID, IntCustomerID, IntArticleID, strSerial, strSerialOrder, IntQuantity, Price, ' . implode(',', $format) . '	FROM ' . SHOP_TABLE . ' WHERE IntOrderID = ' . intval($_REQUEST["bid"]));

	// loop through all articles
	while($GLOBALS['DB_WE']->next_record()) {

		// get all needed information for order-data
		$_REQUEST["cid"] = $GLOBALS['DB_WE']->f("IntCustomerID");
		$SerialOrder[] = $GLOBALS['DB_WE']->f("strSerialOrder");
		foreach(weShopStatusMails::$StatusFields as $field){
			$_REQUEST[$field] = $GLOBALS['DB_WE']->f($field);
		}
		foreach(weShopStatusMails::$MailFields as $field){
			$_REQUEST[$field] = $GLOBALS['DB_WE']->f($field);
		}

		// all information for article
		$ArticleId[] = $GLOBALS['DB_WE']->f("IntArticleID"); // id of article (object or document) in shopping cart
		$tblOrdersId[] = $GLOBALS['DB_WE']->f("IntID");
		$Quantity[] = $GLOBALS['DB_WE']->f("IntQuantity");
		$Serial[] = $GLOBALS['DB_WE']->f("strSerial"); // the serialised doc
		$Price[] = str_replace(',', '.', $GLOBALS['DB_WE']->f("Price")); // replace , by . for float values
	}
	if(!isset($ArticleId)){
		echo we_html_element::jsElement('parent.parent.frames.shop_header_icons.location.reload();') . '
	</head>
	<body class="weEditorBody" onunload="doUnload()">
	<table border="0" cellpadding="0" cellspacing="2" width="300">
      <tr>
        <td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout("<span class='defaultfont'>" . g_l('modules_shop', '[orderDoesNotExist]') . "</span>", g_l('modules_shop', '[loscht]')) . '</td>
      </tr>
      </table></html>';
		exit;
	}
	//
	// first get all information about orders, we need this for the rest of the page
	// ********************************************************************************
	// ********************************************************************************
	// no get information about complete order
	// - pay VAT?
	// - prices are net?
	if(!empty($ArticleId)){

		// first unserialize order-data
		if(!empty($SerialOrder[0])){
			$orderData = @unserialize($SerialOrder[0]);
			$customCartFields = isset($orderData[WE_SHOP_CART_CUSTOM_FIELD]) ? $orderData[WE_SHOP_CART_CUSTOM_FIELD] : array();
		} else{
			$orderData = array();
			$customCartFields = array();
		}

		// prices are net?
		$pricesAreNet = (isset($orderData[WE_SHOP_PRICE_IS_NET_NAME]) ? $orderData[WE_SHOP_PRICE_IS_NET_NAME] : true);

		// must calculate vat?
		$calcVat = (isset($orderData[WE_SHOP_CALC_VAT]) ? $orderData[WE_SHOP_CALC_VAT] : true);
	}
	//
	// no get information about complete order
	// ********************************************************************************
	// ********************************************************************************
	// Building table with customer and order data fields - start
	//
	$customerFieldTable = '';

	// determine all fields for order head
	$fl = 0;

	// first show fields Forename and surname
	if(isset($_customer['Forename'])){
		$customerFieldTable .='
<tr height="25">
	<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_customer', '[Forname]') . ':</td>
	<td class="defaultfont" valign="top" width="40" height="25"></td>
	<td width="20" height="25"></td>
	<td class="defaultfont" valign="top" colspan="6" height="25">' . $_customer['Forename'] . '</td>
</tr>';
	}
	if(isset($_customer['Surname'])){
		$customerFieldTable .='
<tr height="25">
	<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_customer', '[Surname]') . ':</td>
	<td class="defaultfont" valign="top" width="40" height="25"></td>
	<td width="20" height="25"></td>
	<td class="defaultfont" valign="top" colspan="6" height="25">' . $_customer['Surname'] . '</td>
</tr>';
	}

	foreach($_customer as $key => $value){

		if(in_array($key, $fields['customerFields']) || in_array($key, $fields['orderCustomerFields'])){
			if($key == $CLFields['stateField'] && $CLFields['stateFieldIsISO']){
				$value = g_l('countries', '[' . $value . ']');
			}
			if($key == $CLFields['languageField'] && $CLFields['languageFieldIsISO']){
				$value = g_l('countries', '[' . $value . ']');
			}
			$customerFieldTable .='
<tr height="25">
	<td class="defaultfont" width="86" valign="top" height="25">' . $key . ':</td>
	<td class="defaultfont" valign="top" width="40" height="25"></td>
	<td width="20" height="25"></td>
	<td class="defaultfont" valign="top" colspan="6" height="25">' . $value . '</td>
</tr>';
		}
	}



	$orderDataTable = '
<table cellpadding="0" cellspacing="0" border="0" width="99%" class="defaultfont">';
	foreach(weShopStatusMails::$StatusFields as $field){
		if(!$weShopStatusMails->FieldsHidden[$field]){
			$EMailhandler = $weShopStatusMails->getEMailHandlerCode(substr($field, 4), $_REQUEST[$field]);
			$orderDataTable .= '
	<tr height="25">
		<td class="defaultfont" width="86" valign="top" height="25">' . ($field == 'DateOrder' ? g_l('modules_shop', '[bestellnr]') : '') . '</td>
		<td class="defaultfont" valign="top" width="40" height="25"><b>' . ($field == 'DateOrder' ? $_REQUEST['bid'] : '') . '</b></td>
		<td width="20" height="25">' . we_html_tools::getPixel(34, 15) . '</td>
		<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText[$field] . '</td>
		<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
		<td width="14" class="defaultfont" align="right" height="25">
			<div id="div_Calendar_' . $field . '">' . (($_REQUEST[$field] == $dateform) ? '-' : $_REQUEST[$field]) . '</div>
			<input type="hidden" name="' . $field . '" id="hidden_Calendar_' . $field . '" value="' . (($_REQUEST[$field] == $dateform) ? '-' : $_REQUEST[$field]) . '" />
		</td>
		<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
		<td width="102" valign="top" height="25">' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, 'button_Calendar_' . $field) . '</td>
		<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
	</tr>';
		}
	}
	$orderDataTable .= '
	<tr height="5">
		<td class="defaultfont" width="86" valign="top" height="5"></td>
		<td class="defaultfont" valign="top" height="5" width="40"></td>
		<td height="5" width="20"></td>
		<td width="98" class="defaultfont" valign="top" height="5"></td>
		<td height="5"></td>
		<td width="14" class="defaultfont" align="right" valign="top" height="5"></td>
		<td height="5"></td>
		<td width="102" valign="top" height="5"></td>
		<td width="30" height="5">' . we_html_tools::getPixel(30, 5) . '</td>
	</tr>
	<tr height="1">
		<td class="defaultfont" valign="top" colspan="9" bgcolor="grey" height="1">' . we_html_tools::getPixel(14, 1) . '</td>
	</tr>
	<tr>
		<td class="defaultfont" width="86" valign="top"></td>
		<td class="defaultfont" valign="top" width="40"></td>
		<td width="20"></td>
		<td width="98" class="defaultfont" valign="top"></td>
		<td></td>
		<td width="14" class="defaultfont" align="right" valign="top"></td>
		<td></td>
		<td width="102" valign="top"></td>
		<td width="30">' . we_html_tools::getPixel(30, 5) . '</td>
	</tr>' . $customerFieldTable . '
	<tr>
		<td colspan="9"><a href="javascript:we_cmd(\'edit_order_customer\');">' . g_l('modules_shop', '[order][edit_order_customer]') . '</a></td>
	</tr>
	<tr>
		<td colspan="9">' . (we_hasPerm("EDIT_CUSTOMER") ? '<a href="javascript:we_cmd(\'edit_customer\');">' . g_l('modules_shop', '[order][open_customer]') . '</a>' : '') . ' </td>
	</tr>
</table>';
	//
	// end of "Building table with customer fields"
	// ********************************************************************************
	// ********************************************************************************
	// "Building the order infos"
	//

	// headline here - these fields are fix.
	$pixelImg = we_html_tools::getPixel(14, 15);
	$orderTable = '
<table border="0" cellpadding="0" cellspacing="0" width="99%" class="defaultfont">
	<tr>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[anzahl]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Titel]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Beschreibung]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Preis]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Gesamt]') . '</th>
		' . ($calcVat ? '<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[mwst]') . '</th>' : '' ) . '
	</tr>';


	$articlePrice = 0;
	$totalPrice = 0;
	$articleVatArray = array();
	// now loop through all articles in this order
	for($i = 0; $i < count($ArticleId); $i++){

		// now init each article
		$shopArticleObject = (empty($Serial[$i]) ? // output 'document-articles' if $Serial[$d] is empty. This is when an order has been extended
				// this should not happen any more
				we_shop_Basket::getserial($ArticleId[$i], 'w') :
				// output if $Serial[$i] is not empty. This is when a user ordered an article online
				$shopArticleObject = @unserialize($Serial[$i]));

		// now determine VAT
		$articleVat = (isset($shopArticleObject[WE_SHOP_VAT_FIELD_NAME]) ?
				$shopArticleObject[WE_SHOP_VAT_FIELD_NAME] :
				((isset($mwst)) ?
					$mwst :
					0));

		// determine taxes - correct price, etc.
		$Price[$i]/=($pricesAreNet || $calcVat ? 1 : (100 + $articleVat) / 100);
		$articlePrice = $Price[$i] * $Quantity[$i];
		$totalPrice += $articlePrice;

		// calculate individual vat for each article
		if($calcVat){

			if($articleVat > 0){
				if(!isset($articleVatArray[$articleVat])){ // avoid notices
					$articleVatArray[$articleVat] = 0;
				}
				$articleVatArray[$articleVat] += ($articlePrice * $articleVat / (100 + ($pricesAreNet ? 0 : $articleVat)));
			}
		}

		// table row of one article
		$orderTable .= '
<tr><td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td></tr>
<tr>
	<td class="shopContentfontR">' . "<a href=\"javascript:var anzahl=prompt('" . g_l('modules_shop', '[jsanz]') . "','" . $Quantity[$i] . "'); if(anzahl != null){if(anzahl.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&anzahl='+anzahl;}}\">" . $Quantity[$i] . "</a>" . '</td>
	<td></td>
	<td>' . getFieldFromShoparticle($shopArticleObject, 'shoptitle', 35) . '</td>
	<td></td>
	<td>' . getFieldFromShoparticle($shopArticleObject, 'shopdescription', 45) . '</td>
	<td></td>
	<td class="shopContentfontR">' . "<a href=\"javascript:var preis = prompt('" . g_l('modules_shop', '[jsbetrag]') . "','" . $Price[$i] . "'); if(preis != null ){if(preis.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . "}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&preis=' + preis; } }\">" . we_util_Strings::formatNumber($Price[$i]) . "</a>" . $waehr . '</td>
	<td></td>
	<td class="shopContentfontR">' . we_util_Strings::formatNumber($articlePrice) . $waehr . '</td>
	' . ($calcVat ? '
		<td></td>
		<td class="shopContentfontR small">(' . "<a href=\"javascript:var vat = prompt('" . g_l('modules_shop', '[keinezahl]') . "','" . $articleVat . "'); if(vat != null ){if(vat.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&vat=' + vat; } }\">" . we_util_Strings::formatNumber($articleVat) . "</a>" . '%)</td>' :
				'') . '
	<td>' . $pixelImg . '</td>
	<td>' . we_button::create_button("image:btn_function_trash", "javascript:check=confirm('" . g_l('modules_shop', '[jsloeschen]') . "'); if (check){document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&deleteaartikle=" . $tblOrdersId[$i] . "';}", true, 100, 22, "", "", !we_hasPerm("DELETE_SHOP_ARTICLE")) . '</td>
</tr>';
		// if this article has custom fields or is a variant - we show them in a extra rows
		// add variant.
		if(isset($shopArticleObject['WE_VARIANT']) && $shopArticleObject['WE_VARIANT']){

			$orderTable .='
<tr>
	<td colspan="4"></td>
	<td class="small" colspan="6">' . g_l('modules_shop', '[variant]') . ': ' . $shopArticleObject['WE_VARIANT'] . '</td>
</tr>';
		}
		// add custom fields
		if(isset($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && is_array($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && count($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD])){

			$caField = '';
			foreach($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD] as $key => $value){
				$caField .= "$key: $value; ";
			}

			$orderTable .='
<tr>
	<td colspan="4"></td>
	<td class="small" colspan="6">' . $caField . '</td>
</tr>';
		}
	}

	// "Sum of order"
	// add shipping to costs
	if(isset($orderData[WE_SHOP_SHIPPING])){

		// just calculate netPrice, gros, and taxes

		if(!isset($articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']])){
			$articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']] = 0;
		}

		if($orderData[WE_SHOP_SHIPPING]['isNet']){ // all correct here
			$shippingCostsNet = $orderData[WE_SHOP_SHIPPING]['costs'];
			$shippingCostsVat = $orderData[WE_SHOP_SHIPPING]['costs'] * $orderData[WE_SHOP_SHIPPING]['vatRate'] / 100;
			$shippingCostsGros = $shippingCostsNet + $shippingCostsVat;
		} else{
			$shippingCostsGros = $orderData[WE_SHOP_SHIPPING]['costs'];
			$shippingCostsVat = $orderData[WE_SHOP_SHIPPING]['costs'] / ($orderData[WE_SHOP_SHIPPING]['vatRate'] + 100) * $orderData[WE_SHOP_SHIPPING]['vatRate'];
			$shippingCostsNet = $orderData[WE_SHOP_SHIPPING]['costs'] / ($orderData[WE_SHOP_SHIPPING]['vatRate'] + 100) * 100;
		}
		$articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']] += $shippingCostsVat;
	}

	$orderTable .= '
<tr>
	<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
</tr>
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[Preis]') . ':</td>
	<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPrice) . $waehr . '</strong></td>
</tr>';

	if($calcVat){ // add Vat to price
		$totalPriceAndVat = $totalPrice;

		if($pricesAreNet){ // prices are net
			$orderTable .= '<tr><td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td></tr>';

			if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsNet)){

				$totalPriceAndVat += $shippingCostsNet;
				$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
	<td colspan="4" class="shopContentfontR"><strong><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_util_Strings::formatNumber($shippingCostsNet) . $waehr . '</a></strong></td>
	<td></td>
	<td class="shopContentfontR small">(' . we_util_Strings::formatNumber($orderData[WE_SHOP_SHIPPING]['vatRate']) . '%)</td>
</tr>
<tr>
	<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
</tr>';
			}
			$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[plusVat]') . '</label>:</td>
	<td colspan="7"></td>
	<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
</tr>';
			foreach($articleVatArray as $vatRate => $sum){
				if($vatRate){
					$totalPriceAndVat += $sum;
					$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
	<td colspan="4" class="shopContentfontR">' . we_util_Strings::formatNumber($sum) . $waehr . '</td>
</tr>';
				}
			}
			$orderTable .= '
<tr>
	<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
</tr>
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
	<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPriceAndVat) . $waehr . '</strong></td>
</tr>';
		} else{ // prices are gros
			$orderTable .= '<tr><td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td></tr>';

			if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsGros)){
				$totalPrice += $shippingCostsGros;
				$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
	<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_util_Strings::formatNumber($shippingCostsGros) . $waehr . '</a></td>
	<td></td>
	<td class="shopContentfontR small">(' . we_util_Strings::formatNumber($orderData[WE_SHOP_SHIPPING]['vatRate']) . '%)</td>
</tr>
<tr>
	<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
</tr>
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
	<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPrice) . $waehr . '</strong></td>
</tr>
<tr>
	<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
</tr>';
			}

			$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[includedVat]') . '</label>:</td>
	<td colspan="7"></td>
	<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
</tr>';
			foreach($articleVatArray as $vatRate => $sum){
				if($vatRate){
					$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
	<td colspan="4" class="shopContentfontR">' . we_util_Strings::formatNumber($sum) . $waehr . '</td>
</tr>';
				}
			}
		}
	} else{

		if(isset($shippingCostsNet)){
			$totalPrice += $shippingCostsNet;

			$orderTable .= '
<tr>
	<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
</tr>
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
	<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\')">' . we_util_Strings::formatNumber($shippingCostsNet) . $waehr . '</a></td>
</tr>
<tr>
	<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
</tr>
<tr>
	<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
	<td colspan="7"></td>
	<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
</tr>
<tr>
	<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
</tr>
<tr>
	<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
	<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPrice) . $waehr . '</strong></td>
</tr>
<tr>
	<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
</tr>';
		} else{

			$orderTable .= '
<tr>
	<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
	<td colspan="7"></td>
	<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
</tr>';
		}
	}
	$orderTable .= '</table>';
	//
	// "Sum of order"
	// ********************************************************************************
	// ********************************************************************************
	// "Additional fields in shopping basket"
	//

	// at last add custom shopping fields to order
// table with orders ends here

	$customCartFieldsTable = '<table cellpadding="0" cellspacing="0" border="0" width="99%">
			<tr>
				<th colspan="3" class="defaultgray" height="30">' . g_l('modules_shop', '[order_comments]') . '</th>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>';

	foreach($customCartFields as $key => $value){
		$customCartFieldsTable .= '<tr>
				<td class="defaultfont" valign="top"><b>' . $key . ':</b></td>
				<td>' . $pixelImg . '</td>
				<td class="defaultfont" valign="top">' . nl2br($value) . '</td>
				<td>' . $pixelImg . '</td>
				<td valign="top">' . we_button::create_button('image:btn_edit_edit', "javascript:we_cmd('edit_shop_cart_custom_field','" . $key . "');") . '</td>
				<td>' . $pixelImg . '</td>
				<td valign="top">' . we_button::create_button('image:btn_function_trash', "javascript:check=confirm('" . sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field]'), $key) . "'); if (check) { document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?we_cmd[0]=delete_shop_cart_custom_field&bid=" . $_REQUEST["bid"] . "&cartfieldname=" . $key . "'; }") . '</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>';
	}
	$customCartFieldsTable .= '<tr>
				<td>' . we_button::create_button('image:btn_function_plus', "javascript:we_cmd('edit_shop_cart_custom_field');") . '</td>
			</tr>
			</table>';


	//
	// "Additional fields in shopping basket"
	// ********************************************************************************
	//
	// "Building the order infos"
	// ********************************************************************************
	// ********************************************************************************
	// "Html output for order with articles"
	//
echo we_html_element::jsScript(JS_DIR . "jscalendar/calendar.js") .
	we_html_element::jsScript(JS_DIR . "jscalendar/calendar-setup.js") .
	we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/calendar.js") .
	we_html_element::jsScript(JS_DIR . 'images.js') .
	we_html_element::jsScript(JS_DIR . 'windows.js') .
	we_html_element::cssLink(JS_DIR . 'jscalendar/skins/aqua/theme.css');
	?>

	<script type="text/javascript">
		function SendMail(was) {
			document.location = "<?php print $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"]; ?>&SendMail=" + was;
		}
		function doUnload() {
			if (!!jsWindow_count) {
				for (i = 0; i < jsWindow_count; i++) {
					eval("jsWindow" + i + "Object.close()");
				}
			}
		}

		function we_cmd() {

			var args = "";
			var url = "<?php print $_SERVER['SCRIPT_NAME']; ?>?";

			for (var i = 0; i < arguments.length; i++) {
				url += "we_cmd[" + i + "]=" + escape(arguments[i]);
				if (i < (arguments.length - 1)) {
					url += "&";
				}
			}

			switch (arguments[0]) {

				case "edit_shipping_cost":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>", "edit_shipping_cost", -1, -1, 545, 205, true, true, true, false);
					break;

				case "edit_shop_cart_custom_field":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>&cartfieldname=" + (arguments[1] ? arguments[1] : ''), "edit_shop_cart_custom_field", -1, -1, 545, 300, true, true, true, false);
					break;

				case "edit_order_customer":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>", "edit_order_customer", -1, -1, 545, 600, true, true, true, false);
					break;
				case "edit_customer":
					top.document.location = '<?php print WE_MODULES_DIR; ?>show_frameset.php?mod=customer&sid=<?php print $_REQUEST["cid"]; ?>';
					break;
				case "add_new_article":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>", "add_new_article", -1, -1, 650, 600, true, false, true, false);
					break;
			}
		}

		function neuerartikel() {
			we_cmd("add_new_article");
		}

		function deleteorder() {
			top.content.shop_properties.location = "<?php print WE_SHOP_MODULE_DIR; ?>edit_shop_properties.php?deletethisorder=1&bid=<?php echo $_REQUEST["bid"]; ?>";
			top.content.deleteEntry(<?php echo $_REQUEST["bid"]; ?>);
		}

		hot = 1;
	<?php
	if(isset($alertMessage)){
		print we_message_reporting::getShowMessageCall($alertMessage, $alertType);
	}
	?>
	</script>

	</head>
	<body class="weEditorBody" onUnload="doUnload()">

		<?php
		$parts = array(array(
				"html" => $orderDataTable,
				"space" => 0
			),
			array(
				"html" => $orderTable,
				"space" => 0
			)
		);
		if($customCartFieldsTable){

			$parts[] = array(
				"html" => $customCartFieldsTable,
				"space" => 0
			);
		}

		print we_multiIconBox::getHTML("", "100%", $parts, 30);

		//
		// "Html output for order with articles"
		// ********************************************************************************
	} else{ // This order has no more entries
		echo we_html_element::jsElement('
		top.content.shop_properties.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_properties.php?deletethisorder=1&bid=' . $_REQUEST["bid"] . '";
		top.content.deleteEntry(' . $_REQUEST["bid"] . ');
	') . '
</head>
<body bgcolor="#ffffff">';
	}

	$js = '
			// init the used calendars

			function CalendarChanged(calObject) {
				// field:
				_field = calObject.params.inputField;
				document.location = "' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&" + _field.name + "=" + _field.value;
			}';

	foreach(weShopStatusMails::$StatusFields as $cur){
		if(!$weShopStatusMails->FieldsHidden[$cur]){
			$js.='		Calendar.setup({
		"inputField" : "hidden_Calendar_' . $cur . '",
		"displayArea" : "div_Calendar_' . $cur . '",
		"button" : "date_pickerbutton_Calendar_' . $cur . '",
		"ifFormat" : "' . $da . '",
		"daFormat" : "' . $da . '",
		"onUpdate" : CalendarChanged
		});';
		}
	}
	echo we_html_element::jsElement($js);
	?>
</body>
</html>
