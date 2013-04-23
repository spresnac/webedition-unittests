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
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');
we_html_tools::protect();
$parts = array();
$jsCode = "
var _oCsv_;
var _sInitCsv_;
var _sUpbInc='upb/upb';
var _bPrev=false;
var _sLastPrevCsv='';

function init(){
	_fo=document.forms[0];
	_oCsv_=opener.gel(_sObjId+'_csv')
	var sCsv=_oCsv_.value;
	_sInitCsv_=sCsv;
	var oChbxType=_fo.elements['chbx_type'];
	var iChbxTypeLen=oChbxType.length;
	if(typeof iChbxTypeLen!='undefined'){
		for(var i=iChbxTypeLen-1;i>=0;i--){
			oChbxType[i].checked=(parseInt(sCsv.charAt(i)))?true:false;
		}
	}else{
		oChbxType.checked=(parseInt(sCsv.charAt(0)))?true:false;
	}
	initPrefs();
}

function getBinary(){
	var sBinary='';
	var oChbx=_fo.elements['chbx_type'];
";

if(defined("FILE_TABLE") && defined("OBJECT_FILES_TABLE") && we_hasPerm("CAN_SEE_OBJECTFILES")){
	$jsCode .= "
	var iChbxLen=oChbx.length;
	for(var i=0;i<iChbxLen;i++){
		sBinary+=(oChbx[i].checked)?'1':'0';
	}
";
} else{
	$jsCode .= "
	sBinary+=(oChbx.checked)?'10':'00';
";
}

$jsCode .= "
	return sBinary;
}

function save(){
	var sCsv=getBinary();
	_oCsv_.value=sCsv;
	if((!_bPrev&&_sInitCsv_!=sCsv)||(_bPrev&&_sLastPrevCsv!=sCsv)){
		opener.rpc(sCsv,'','','','',_sObjId,_sUpbInc);
	}
	previewPrefs();
	" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[prefs_saved_successfully]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
	self.close();
}

function preview(){
	_bPrev=true;
	var sCsv=getBinary();
	_sLastPrevCsv=sCsv;
	previewPrefs();
	opener.rpc(sCsv,'','','','',_sObjId,_sUpbInc);
}

function exit_close(){
	if(_sInitCsv_!=getBinary()&&_bPrev){
		opener.rpc(_sInitCsv_,'','','','',_sObjId,_sUpbInc);
	}
	exitPrefs();
	self.close();
}
";

$oChbxDocs = we_forms::checkbox(
		$value = 0, $checked = true, $name = "chbx_type", $text = g_l('cockpit', '[documents]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxObjs = we_forms::checkbox(
		$value = 0, $checked = true, $name = "chbx_type", $text = g_l('cockpit', '[objects]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$dbTableType = "<table><tr>";
if(defined("FILE_TABLE"))
	$dbTableType .= "<td>" . $oChbxDocs . "</td><td>" . we_html_tools::getPixel(10, 1) . "</td>";
if(defined("OBJECT_FILES_TABLE") && we_hasPerm("CAN_SEE_OBJECTFILES"))
	$dbTableType .= "<td>" . $oChbxObjs . "</td>";
$dbTableType .= "</tr></table>";

array_push($parts, array(
	"headline" => g_l('cockpit', '[type]'), "html" => $dbTableType, "space" => 80
));
array_push($parts, array(
	"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
));

$save_button = we_button::create_button("save", "javascript:save();", false, -1, -1);
$preview_button = we_button::create_button("preview", "javascript:preview();", false, -1, -1);
$cancel_button = we_button::create_button("close", "javascript:exit_close();");
$buttons = we_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_multiIconBox::getHTML(
		"mfdProps", "100%", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[unpublished]'));

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[unpublished]')) . STYLESHEET . we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
			we_html_element::jsElement(
				$jsPrefs . $jsCode . we_button::create_state_changer(false))) . we_html_element::htmlBody(
			array(
			"class" => "weDialogBody", "onload" => "init();"
			), we_html_element::htmlForm("", $sTblWidget)));