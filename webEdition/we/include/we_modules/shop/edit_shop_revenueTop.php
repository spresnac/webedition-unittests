<?php
/**
 * webEdition CMS
 *
 * $Rev: 5878 $
 * $Author: mokraemer $
 * $Date: 2013-02-24 03:42:12 +0100 (Sun, 24 Feb 2013) $
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
include_once ($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/util/Strings.php');

$selectedYear = isset($_REQUEST['ViewYear']) ? $_REQUEST['ViewYear'] : date("Y");
$selectedMonth = isset($_REQUEST['ViewMonth']) ? $_REQUEST['ViewMonth'] : '0';
$orderBy = isset($_REQUEST['orderBy']) ? $_REQUEST['orderBy'] : 'IntOrderID';
$actPage = isset($_REQUEST['actPage']) ? $_REQUEST['actPage'] : '0';

function orderBy($a, $b){

	$ret = ($a[$_REQUEST['orderBy']] >= $b[$_REQUEST['orderBy']]);
	return (isset($_REQUEST['orderDesc']) ? !$ret : $ret);
}

function getTitleLink($text, $orderKey){
	$_href = $_SERVER['SCRIPT_NAME'] .
		'?ViewYear=' . $GLOBALS['selectedYear'] .
		'&ViewMonth=' . $GLOBALS['selectedMonth'] .
		'&orderBy=' . $orderKey .
		'&actPage=' . $GLOBALS['actPage'] .
		( ($GLOBALS['orderBy'] == $orderKey && !isset($_REQUEST['orderDesc'])) ? '&orderDesc=true' : '' );

	return '<a href="' . $_href . '">' . $text . '</a>' . ($GLOBALS['orderBy'] == $orderKey ? ' <img src="' . IMAGE_DIR . 'arrow_sort_' . (isset($_REQUEST['orderDesc']) ? 'desc' : 'asc') . '.gif" />' : '');
}

function getPagerLink(){
	return $_SERVER['SCRIPT_NAME'] .
		'?ViewYear=' . $GLOBALS['selectedYear'] .
		'&ViewMonth=' . $GLOBALS['selectedMonth'] .
		'&orderBy=' . $GLOBALS['orderBy'] .
		(isset($_REQUEST['orderdesc']) ? '&orderDesc=true' : '' );
}

function yearSelect($select_name){
	$yearStart = 2001;
	$yearNow = date('Y');
	$opts = array();

	while($yearNow > $yearStart) {
		$opts[$yearNow] = $yearNow;
		$yearNow--;
	}
	return we_class::htmlSelect($select_name, $opts, 1, (isset($_REQUEST[$select_name]) ? $_REQUEST[$select_name] : ''), false, 'id="' . $select_name . '"');
}

function monthSelect($select_name){
	$opts = g_l('modules_shop', '[month]');
	$opts[0] = '-';
	ksort($opts, SORT_NUMERIC);
	return we_class::htmlSelect($select_name, $opts, 1, (isset($_REQUEST[$select_name]) ? $_REQUEST[$select_name] : ''), false, 'id="' . $select_name . '"');
}

we_html_tools::protect();
we_html_tools::htmlTop();

print STYLESHEET .
	we_html_element::jsElement('
	function we_submitDateform() {
		elem = document.forms[0];
		elem.submit();
	}

	var countSetTitle = 0;
	function setHeaderTitle() {
		pre = "";
		post = "' . (isset($_REQUEST['ViewMonth']) && $_REQUEST['ViewMonth'] > 0 ? g_l('modules_shop', '[month][' . $_REQUEST['ViewMonth'] . ']') . " " : "") . $_REQUEST['ViewYear'] . '";
		if(parent.edheader && parent.edheader.setTitlePath) {
			parent.edheader.hasPathGroup = true;
			parent.edheader.setPathGroup(pre);
			parent.edheader.hasPathName = true;
			parent.edheader.setPathName(post);
			parent.edheader.setTitlePath();
			countSetTitle = 0;
		} else {
			if(countSetTitle < 30) {
				setTimeout("setHeaderTitle()",100);
				countSetTitle++;
			}
		}
	}

') . '
<style type="text/css">
	table.revenueTable {
		border-collapse: collapse;
	}
	table.revenueTable th,
	table.revenueTable td {
		padding: 8px;
		border: 1px solid #666666;
	}
</style>
</head>
<body class="weEditorBody" onload="self.focus(); setHeaderTitle();" onunload="">
<form>';

// get some preferences!
$feldnamen = explode('|', f('SELECT strFelder from ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname = "shop_pref"', 'strFelder', $DB_WE));
$waehr = "&nbsp;" . oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$defaultVat = !empty($feldnamen[1]) ? ($feldnamen[1]) : 0;

if(!isset($nrOfPage)){

	$nrOfPage = isset($feldnamen[4]) ? $feldnamen[4] : 20;
}
if($nrOfPage == "default"){
	$nrOfPage = 20;
}

$parts = array(
// get header of total revenue of a year
	array(
		'headline' => '<label for="ViewYear">' . g_l('modules_shop', '[selectYear]') . '</label>',
		'html' => yearSelect("ViewYear"),
		'space' => 150,
		'noline' => 1
	),
	array(
		'headline' => '<label for="ViewMonth">' . g_l('modules_shop', '[selectMonth]') . '</label>',
		'html' => monthSelect("ViewMonth"),
		'space' => 150,
		'noline' => 1
	),
	array(
		'headline' => we_button::create_button('select', "javascript:we_submitDateform();"),
		'html' => '',
		'space' => 150
	)
);

// get queries for revenue and article list.
$queryCondtion = 'date_format(DateOrder,"%Y") = "' . $selectedYear . '"';

if($selectedMonth != '0'){
	$queryCondtion .= ' AND date_format(DateOrder,"%c") = "' . $selectedMonth . '"';
}

$DB_WE->query('SELECT *,DATE_FORMAT(DateOrder, "%d.%m.%Y") AS formatDateOrder, DATE_FORMAT(DatePayment, "%d.%m.%Y") AS formatDatePayment FROM ' . SHOP_TABLE . '	WHERE ' . $queryCondtion . ' ORDER BY ' . (isset($_REQUEST['orderBy']) && $_REQUEST['orderBy'] ? $_REQUEST['orderBy'] : 'IntOrderID'));

if(($maxRows = $DB_WE->num_rows())){

	$actOrder = 0;
	$amountOrders = 0;
	$editedOrders = 0;
	$payedOrders = 0;
	$unpayedOrders = 0;
	$total = 0;
	$payed = 0;
	$unpayed = 0;


	// first of all calculate complete revenue of this year -> important check vats as well.

	$nr = 0;
	$orderRows = array();
	while($DB_WE->next_record()) {

		// for the articlelist, we need also all these article, so sve them in array
		// initialize all data saved for an article
		$shopArticleObject = @unserialize($DB_WE->f('strSerial'));
		$serialOrder = $DB_WE->f('strSerialOrder');
		$orderData = ($serialOrder ? @unserialize($serialOrder) : array());

		if(($nr >= ($actPage * $nrOfPage)) && ($nr < $maxRows) && ($nr < ($actPage * $nrOfPage + $nrOfPage))){
			$orderRows[$nr] = array(
				'articleArray' => $shopArticleObject,
				// save all data in array
				'IntOrderID' => $DB_WE->f('IntOrderID'), // also for ordering
				'IntCustomerID' => $DB_WE->f('IntCustomerID'),
				'IntArticleID' => $DB_WE->f('IntArticleID'), // also for ordering
				'IntQuantity' => $DB_WE->f('IntQuantity'),
				'DatePayment' => $DB_WE->f('DatePayment'),
				'DateOrder' => $DB_WE->f('DateOrder'),
				'formatDateOrder' => $DB_WE->f('formatDateOrder'), // also for ordering
				'formatDatePayment' => $DB_WE->f('formatDatePayment'), // also for ordering
				'Price' => $DB_WE->f('Price'), // also for ordering
				'shoptitle' => (isset($shopArticleObject['shoptitle']) ? $shopArticleObject['shoptitle'] : $shopArticleObject['we_shoptitle']), // also for ordering
				'orderArray' => $orderData,
			);
		}
		// all data from strSerialOrder
		// first unserialize order-data

		$actPrice = 0;


		// ********************************************************************************
		// now get information about complete order
		// - pay VAT?
		// - prices are net?
		// prices are net?
		$pricesAreNet = (isset($orderData[WE_SHOP_PRICE_IS_NET_NAME]) ? $orderData[WE_SHOP_PRICE_IS_NET_NAME] : true);

		// must calculate vat?
		$calcVat = (isset($orderData[WE_SHOP_CALC_VAT]) ? $orderData[WE_SHOP_CALC_VAT] : true);

		//
		// no get information about complete order
		// ********************************************************************************
		// now calculate prices: without vat first
		$actPrice = $DB_WE->f('Price') * $DB_WE->f('IntQuantity');
		// now calculate vats to prices !!!
		if($calcVat){ // vat must be payed for this order
			// now determine VAT
			$articleVat = (isset($shopArticleObject[WE_SHOP_VAT_FIELD_NAME]) ?
					$shopArticleObject[WE_SHOP_VAT_FIELD_NAME] :
					(isset($defaultVat) ? $defaultVat : 0)
				);

			if($articleVat > 0){
				if(!isset($articleVatArray[$articleVat])){ // avoid notices
					$articleVatArray[$articleVat] = 0;
				}

				// calculate vats to prices if neccessary
				if($pricesAreNet){
					$articleVatArray[$articleVat] += ($actPrice * $articleVat / 100);
					$actPrice += ($actPrice * $articleVat / 100);
				} else{
					$articleVatArray[$articleVat] += ($actPrice * $articleVat / (100 + $articleVat));
				}
			}
		}
		$total += $actPrice;


		if($DB_WE->f('DatePayment') != 0){
			if($actOrder != $DB_WE->f('IntOrderID')){
				$payedOrders++;
			}
			$payed += $actPrice;
		} else{
			if($actOrder != $DB_WE->f('IntOrderID')){
				$unpayedOrders++;
			}
			$unpayed += $actPrice;
		}

		if($DB_WE->f('DateShipping') != 0){
			if($actOrder != $DB_WE->f('IntOrderID')){
				$editedOrders++;
			}
		}

		// save last order.
		if($actOrder != $DB_WE->f('IntOrderID')){
			$actOrder = $DB_WE->f('IntOrderID');
			$amountOrders++;
		}
		++$nr;
	}

	// generate vat table
	$vatTable = '';
	if(isset($articleVatArray)){
		$vatTable .= '
<tr>
	<td>' . we_html_tools::getPixel(1, 10) . '</td>
<tr>
	<td colspan="6" class="shopContentfontR">' . g_l('modules_shop', '[includedVat]') . ':</td>
</tr>';
		foreach($articleVatArray as $_vat => $_amount){
			$vatTable .= '
<tr>
	<td colspan="5"></td>
	<td class="shopContentfontR">' . $_vat . '&nbsp;%</td>
	<td class="shopContentfontR">' . we_util_Strings::formatNumber($_amount) . $waehr . '</td>
</tr>
				';
		}
	}

	$parts[] = array(
		'html' => '
<table class="defaultfont" width="680" cellpadding="2">
<tr>
	<th>' . g_l('modules_shop', '[anual]') . '</th>
	<th>' . ($selectedMonth ? g_l('modules_shop', '[monat]') : '' ) . '</th>
	<th>' . g_l('modules_shop', '[anzahl]') . '</th>
	<th>' . g_l('modules_shop', '[unbearb]') . '</th>
	<th>' . g_l('modules_shop', '[schonbezahlt]') . '</th>
	<th>' . g_l('modules_shop', '[unbezahlt]') . '</th>
	<th>' . g_l('modules_shop', '[umsatzgesamt]') . '</th>
</tr>
<tr class="shopContentfont">
	<td>' . $selectedYear . '</td>
	<td>' . ($selectedMonth ? $selectedMonth : '' ) . '</td>
	<td>' . $amountOrders . '</td>
	<td class="npshopContentfontR">' . ($amountOrders - $editedOrders) . '</td>
	<td>' . we_util_Strings::formatNumber($payed) . $waehr . '</td>
	<td class="npshopContentfontR">' . we_util_Strings::formatNumber($unpayed) . $waehr . '</td>
	<td class="shopContentfontR">' . we_util_Strings::formatNumber($total) . $waehr . '</td>
</tr>' .
		$vatTable . '
</table>',
		'space' => 0
	);

	$headline = array(
		array("dat" => getTitleLink(g_l('modules_shop', '[bestellung]'), 'IntOrderID')),
		array("dat" => g_l('modules_shop', '[ArtName]')), // 'shoptitle'
		array("dat" => getTitleLink(g_l('modules_shop', '[artPrice]'), 'Price')),
		array("dat" => getTitleLink(g_l('modules_shop', '[artOrdD]'), 'DateOrder')),
		array("dat" => getTitleLink(g_l('modules_shop', '[ArtID]'), 'IntArticleID')),
		array("dat" => getTitleLink(g_l('modules_shop', '[artPay]'), 'DatePayment')),
	);
	$content = array();

	// we need functionalitty to order these

	/* if(isset($_REQUEST['orderBy']) && $_REQUEST['orderBy']){
	  usort($orderRows, 'orderBy');
	  } */

	for($i = ($actPage * $nrOfPage); $i < $maxRows && $i < ($actPage * $nrOfPage + $nrOfPage); $i++){

		$orderData = $orderRows[$i]['orderArray'];
		$articleData = $orderRows[$i]['articleArray'];

		$variantStr = '';
		if(isset($articleData['WE_VARIANT']) && $articleData['WE_VARIANT']){
			$variantStr = '<br />' . g_l('modules_shop', '[variant]') . ': ' . $articleData['WE_VARIANT'];
		}

		$customFields = '';
		if(isset($articleData[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && $articleData[WE_SHOP_ARTICLE_CUSTOM_FIELD]){
			$customFields = we_html_element::htmlBr();
			foreach($articleData[WE_SHOP_ARTICLE_CUSTOM_FIELD] as $key => $val){
				$customFields .= $key . '=' . $val . we_html_element::htmlBr();
			}
		}

		$content[] = array(
			array('dat' => $orderRows[$i]['IntOrderID']),
			array('dat' => $orderRows[$i]['shoptitle'] . '<span class="small">' . $variantStr . ' ' . $customFields . '</span>'),
			array('dat' => we_util_Strings::formatNumber($orderRows[$i]['Price']) . $waehr),
			array('dat' => $orderRows[$i]['formatDateOrder']),
			array('dat' => $orderRows[$i]['IntArticleID']),
			array('dat' => ($orderRows[$i]['DatePayment'] != 0 ? $orderRows[$i]['formatDatePayment'] : '<span class="npshopContentfontR">' . g_l('modules_shop', '[artNPay]') . '</span>')),
		);
	}

	$parts[] = array(
		'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
		'space' => 0,
		'noline' => true
	);

	$parts[] = array(
		'html' => blaettern::getStandardPagerHTML(getPagerLink(), $actPage, $nrOfPage, $maxRows),
		'space' => 0
	);
} else{
	$parts[] = array(
		'html' => g_l('modules_shop', '[NoRevenue]'),
		'space' => 0
	);
}

print we_multiIconBox::getHTML('revenues', '100%', $parts, 30, '', -1, '', '', false, sprintf(g_l('tabs', '[module][revenueTotal]'), $selectedYear));
?>
</form>
</body>
</html>