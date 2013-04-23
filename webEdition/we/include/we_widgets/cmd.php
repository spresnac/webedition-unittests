<?php

/**
 * webEdition CMS
 *
 * $Rev: 5792 $
 * $Author: mokraemer $
 * $Date: 2013-02-10 19:05:47 +0100 (Sun, 10 Feb 2013) $
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
switch($_REQUEST['we_cmd'][0]){
	case "save" :
		setUserPref("cockpit_dat", $_REQUEST['we_cmd'][1]);
		break;
	case "add" :
		include_once(WE_INCLUDES_PATH . 'we_widgets/cfg.inc.php');

		$aProps = array(
			$_REQUEST['we_cmd'][1],
			$aPrefs[$_REQUEST['we_cmd'][1]]["cls"],
			$aPrefs[$_REQUEST['we_cmd'][1]]["res"],
			$aPrefs[$_REQUEST['we_cmd'][1]]["csv"],
		);
		foreach($aCfgProps as $a){
			foreach($a as $arr){
				if($arr[0] == $aProps[0]){
					$aProps[3] = $arr[3];
					break 2;
				}
			}
		}
		$iCurrId = str_replace('m_', '', $_REQUEST['we_cmd'][2]);
		$iWidth = $aPrefs[$aProps[0]]['width'];
		if($aProps[0] != 'rss' && $aProps[0] != 'pad'){
			if($aProps[0] == "msg")
				$_transact = md5(uniqid(__FUNCTION__, true));
			include_once (WE_INCLUDES_PATH . 'we_widgets/mod/' . $aProps[0] . '.php');
		}
		include_once (WE_INCLUDES_PATH . 'we_widgets/inc/' . $aProps[0] . '.inc.php');

		$js = "
function gel(id_){
	return document.getElementById?document.getElementById(id_):null;
}
function transmit(){
	if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().quickstart){
		top.weEditorFrameController.getActiveDocumentReference().pushContent('" . $aProps[0] . "','m_" . $iCurrId . "',gel('content').innerHTML,gel('prefix').innerHTML,gel('postfix').innerHTML,gel('csv').innerHTML);
	}
}
";
		print we_html_element::htmlDocType() .
			we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_element::cssElement("div,span{display:none;}") . we_html_element::jsElement($js)) .
				we_html_element::htmlBody(
					array("onload" => "transmit();"
					), we_html_element::htmlDiv(array("id" => "content"), $oTblCont->getHtml()) .
					we_html_element::htmlSpan(array("id" => "prefix"), $aLang[0]) .
					we_html_element::htmlSpan(array("id" => "postfix"), $aLang[1]) .
					we_html_element::htmlSpan(array("id" => "csv"), (isset($aProps[3]) ? $aProps[3] : ""))));
		break;

	//added to fix bug #6538
	case 'reset_home':
		$id = intval($_SESSION['user']['ID']);
		//delete user's cockpit preferences from db
		$GLOBALS['DB_WE']->query('REPLACE INTO ' . PREFS_TABLE . ' (`userID`,`key`,`value`) VALUES (' . $id . ',"cockpit_dat",""),(' . $id . ',"cockpit_amount_columns","")');
		include(WE_INCLUDES_PATH . 'home.inc.php');
		break;
}
