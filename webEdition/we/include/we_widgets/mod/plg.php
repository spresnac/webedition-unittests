<?php

/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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
include_once (WE_INCLUDES_PATH . 'we_widgets/inc/plg/chart.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . WE_TRACKER_DIR . "/includes/showme.inc.php");

we_html_tools::protect();

$_url = getServerUrl() . WE_INCLUDES_DIR . 'we_widgets/inc/plg/';

$_isPrev = !isset($aProps);

list($_pLogCsv, $_pLogUrl64) = explode(";", (($_isPrev) ? $_REQUEST['we_cmd'][0] : $aProps[3]));
$_pLogUrl = base64_decode($_pLogUrl64);
$_pLog_[] = array(
	'visitors_data_today',
	'visitors_today_total',
	'visitors_today_unique',
	'lateral_entry_today',
	'pages_today',
	'transfer_today'
);
$_pLog_[] = array(
	'visitors_data_yesterday',
	'visitors_yesterday_total',
	'visitors_yesterday_unique',
	'lateral_entry_yesterday',
	'pages_yesterday',
	'transfer_yesterday'
);
$_pLog_[] = array(
	'visitors_data_this_month',
	'visitors_this_month_total',
	'visitors_this_month_unique',
	'lateral_entry_this_month',
	'pages_this_month',
	'transfer_this_month'
);
$_pLog_[] = array(
	'visitors_behaviour_today',
	'visitors_avg_hour_today',
	'retention_avg_visitor_today',
	'showtime_avg_page_today',
	'impressions_per_visitor_today'
);
$_pLog_[] = array(
	'Snapshot', 'usercount', 'bot_visits', 'downloads', 'visitor_per_hour'
);
$_pLog_[] = array(
	'top_visiting_periods',
	'strongest_visitor_hour',
	'lowest_visitor_hour',
	'strongest_visitor_day',
	'lowest_visitor_day'
);
$_pLog_[] = array(
	'visitors_forecast', 'forecast_today'
);
$_pLog_[] = array(
	'avg_amount_visitors', 'avg_visitors_hour', 'avg_visitors_day', 'avg_visitors_month'
);
$_pLog_[] = array(
	'promo_value_tai', 'promo_value_today', 'promo_value_this_month', 'promo_value_this_year'
);

$_pLogOut = we_html_element::cssElement(
		"TD{font-family:arial,verdana;color:#2f2f2f;font-size:11px;line-height:16px}
.tablehead{padding-left:2px;background-color:#cccccc;font-size:10px;color:#000000;font-weight:bold;}
.boxbg{padding-left:5px;font-size:10px;color:black;background-color:#F8F8F8;}
.resbg{padding-left:5px;font-size:10px;color:black;background-color:#EFEFEF;}
.bulletCircle{width:7px;height:7px;}
.finelinebox{border-right:#666666 1px solid;border-top:#666666 1px solid;border-left:#666666 1px solid;border-bottom:#666666 1px solid;}");

$_gap = false;
for($i = 0; $i <= 10; $i++){
	if($_pLogCsv[$i]){
		if($_gap){
			$_pLogOut .= we_html_tools::getPixel(1, 8) . we_html_element::htmlBr();
		} else{
			$_gap = true;
		}
		if($i <= 8){
			$_pLogChart = getPLogChart($_pLog_[$i]);
			$_pLogOut .= $_pLogChart->getHTML();
		}
	}
}

if($_isPrev){
	$sJsCode = "
	var _sObjId='" . $_REQUEST['we_cmd'][5] . "';
	var _sType='plg';
	var _sTb='" . g_l('cockpit', '[pagelogger]') . ($_pLogUrl != '' ? ' - ' . $_pLogUrl : $_pLogUrl) . "';

	function init(){
		parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
	}
	";

	print we_html_element::htmlDocType() . we_html_element::htmlHtml(
			we_html_element::htmlHead(
				we_html_tools::getHtmlInnerHead(g_l('cockpit', '[pagelogger]')) . STYLESHEET . we_html_element::jsElement(
					$sJsCode)) . we_html_element::htmlBody(
				array(
				"marginwidth" => "15",
				"marginheight" => "10",
				"leftmargin" => "15",
				"topmargin" => "10",
				"onload" => "if(parent!=self)init();"
				), we_html_element::htmlDiv(array(
					"id" => "plg"
					), $_pLogOut)));
} else{
	$_pLog = new we_html_table(array(
			"width" => "100%", "border" => "0", "cellpadding" => "0", "cellspacing" => "0"
			), 1, 1);
	$_pLog->setCol(0, 0, null, $_pLogOut);
}
