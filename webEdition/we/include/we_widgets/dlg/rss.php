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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');

we_html_tools::protect();
$jsCode = "
var _aTopRssFeeds_=opener._trf;
var _iTopRssFeedsLen=_aTopRssFeeds_.length;
var _bIsHotTopRssFeeds=false;
var _sInitUri;
var _sLastPreviewUri='';
var _sInitRssCfg='';
var _iInitRssCfgNumEntries=0;
var _sInitTbCfg='';
var _iInitTbTitlePers=0;

function gel(id_){
	return document.getElementById?document.getElementById(id_):null;
}

function isUrl(s) {
	var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(s);
}

function populateSct(oSctRss){
	for(var i=0;_iTopRssFeedsLen>i;i++){
		var sOptVal=opener.base64_decode(_aTopRssFeeds_[i][1]);
		var sOptTxt=opener.base64_decode(_aTopRssFeeds_[i][0]);" .
	(!we_base_browserDetect::isSafari() ? "
			var opt=document.createElement('OPTION');
			opt.value=opener.base64_decode(_aTopRssFeeds_[i][1]);
			opt.text=opener.base64_decode(_aTopRssFeeds_[i][0]);
			oSctRss.options.add(opt,i+1);" :
		"oSctRss.options[oSctRss.options.length] = new Option(sOptTxt,sOptVal);"
	) . "
	}
}

function handleButtonState(){
	var iArgsLen=arguments.length;
	var sImplodeArgs='';
	for(var i=1;i<iArgsLen;i++){
		sImplodeArgs+='\''+arguments[i]+'\''+((i<iArgsLen-1)?',':'');
	}
	eval('var aDisable=['+sImplodeArgs+']');
	for(var i=0;i<iArgsLen-1;i++){
		switch_button_state(aDisable[i],aDisable[i]+'_enabled',(arguments[0])?'enabled':'disabled');
	}
}

function toggleRssTopFeed(){
	var oSctRss=_fo.elements['sct_rss'];
	var sUri=oSctRss.options[oSctRss.selectedIndex].value;
	var sTitle=oSctRss.options[oSctRss.selectedIndex].text;
	var oIptNewUri=_fo.elements['ipt_newUri'];
	var oIptNewTitle=_fo.elements['ipt_newTitle'];
	oIptNewUri.value=sUri;
	oIptNewTitle.value=sTitle;
	handleButtonState(oSctRss.selectedIndex,'overwrite','delete');
}

function init(){
	_fo=document.forms[0];
	var sCsv_=opener.gel(_sObjId+'_csv').value;
	var aCsv=sCsv_.split(',');
	var sUri=opener.base64_decode(aCsv[0]);
	_sInitUri=sUri;
	_sInitRssCfg=aCsv[1];
	var oSctRss=_fo.elements['sct_rss'];
	populateSct(oSctRss);
	var iSctRssLen=oSctRss.length;
	_fo.elements['ipt_uri'].value=sUri;
	_fo.elements['ipt_uri'].title=sUri;

	for(var i=iSctRssLen-1;i>=0;i--){
		oSctRss.options[i].selected=(oSctRss.options[i].value==sUri)?true:false;
	}
	toggleRssTopFeed();
	var oChbxConf=_fo.elements['chbx_conf'];
	var iChbxConfLen=oChbxConf.length;
	for(var i=iChbxConfLen-1;i>=0;i--){
		oChbxConf[i].checked=(parseInt(_sInitRssCfg.charAt(i)))?true:false;
	}
	var oSctConf=_fo.elements['sct_conf'];
	_iInitRssCfgNumEntries=aCsv[2];
	oSctConf.options[_iInitRssCfgNumEntries].selected=true;
	_sInitTbCfg=aCsv[3];
	var oChbxTb=_fo.elements['chbx_tb'];
	var iChbxTbLen=oChbxTb.length;
	for(var i=iChbxTbLen-1;i>=0;i--){
		oChbxTb[i].checked=(parseInt(aCsv[3].charAt(i)))?true:false;
	}
	_iInitTbTitlePers=aCsv[4];
	var oRdoTitle=_fo.elements['rdo_title'];
	oRdoTitle[aCsv[4]].checked=true;
	initPrefs();
}

function onChangeSctRss(obj){
	var sUri=obj.options[obj.selectedIndex].value;
	var sTitle=obj.options[obj.selectedIndex].text;
	toggleRssTopFeed();
	if(sUri!=''){
		var oIptUri=_fo.elements['ipt_uri'];
		oIptUri.value=sUri;
		oIptUri.title=sUri;
	}
}

function handleTopRssFeed(sAction){
	var oIptUri=_fo.elements['ipt_uri'];
	var oSctRss=_fo.elements['sct_rss'];
	var iSelIdx=oSctRss.selectedIndex;
	var oIptNewTitle=_fo.elements['ipt_newTitle'];
	var sNewTitle=oIptNewTitle.value;
	var oIptNewUri=_fo.elements['ipt_newUri'];
	var sNewUri=oIptNewUri.value;
	switch(sAction){
		case 'overwrite':
			oSctRss.options[iSelIdx].text=oIptNewTitle.value;
			oSctRss.options[iSelIdx].value=oIptNewUri.value;
			oIptUri.value=oSctRss.options[iSelIdx].value;
			break;
		case 'add':
			if(sNewTitle!=''&&sNewUri!=''){
				if(oSctRss.length<=1){
					var newOpt1=new Option(sNewTitle,sNewUri);
					oSctRss.options[1]=newOpt1;
					oSctRss.selectedIndex=1;
				}else if(iSelIdx!=-1){
					var aSctText=new Array();
					var aSctValues=new Array();
					var iCount=-1;
					var iNewSelected=-1;
					for(var i=0;i<oSctRss.length;i++){
						iCount++;
						if(iCount==iSelIdx){
							aSctText[(iSelIdx==0&&iCount==0)?1:iCount]=sNewTitle;
							aSctValues[(iSelIdx==0&&iCount==0)?1:iCount]=sNewUri;
							iNewSelected=(iSelIdx==0&&iCount==0)?1:iCount;
							iCount++;
						}
						aSctText[(iSelIdx==0&&iCount==1)?0:iCount]=oSctRss.options[i].text;
						aSctValues[(iSelIdx==0&&iCount==1)?0:iCount]=oSctRss.options[i].value;
					}
					for(var i=0;i<=iCount;i++){
						var newOpt=new Option(aSctText[i],aSctValues[i]);
						oSctRss.options[i]=newOpt;
						oSctRss.options[i].selected=(i==iNewSelected)?true:false;
					}
				}
				handleButtonState(1,'overwrite','delete');
				_iTopRssFeedsLen++;
			}else{
				" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[prefs_saved_successfully]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
			}
			break;
		case 'delete':
			if(iSelIdx>=1){
				oSctRss.options[iSelIdx]=null;
				oSctRss.selectedIndex=0;
				oIptNewTitle.value=oIptNewUri.value='';
				handleButtonState(0,'overwrite','delete');
				_iTopRssFeedsLen--;
			}
			break;
	}
	_bIsHotTopRssFeeds=true;
}

function onDisableRdoGroup(sId){
	var oDisable=_fo.elements['rdo_'+sId];
	var iDisableLen=oDisable.length;
	for(var i=0;iDisableLen>i;i++){
		oDisable[i].disabled=(!oDisable[i].disabled)?true:false;
	}
}

function getTbPersTitle(sUri){
	var oRdoTitle=_fo.elements['rdo_title'];
	var sTbTitle='';
	if(oRdoTitle[1].checked==true){
		var oSctRss=_fo.elements['sct_rss'];
		for(var i=1;_iTopRssFeedsLen>i;i++){
			if(oSctRss.options[i].value==sUri){
				sTbTitle=oSctRss.options[i].text;
				break;
			}
		}
	}
	return sTbTitle;
}

function displayRssFeed(sUri,bOnChange){
	var sRssCfgBinary=getBinary('conf');
	var sRssCfgSelIdx=_fo.elements['sct_conf'].selectedIndex;
	if(!bOnChange||(_sLastPreviewUri!=''&&sUri!=_sLastPreviewUri)||(_sLastPreviewUri==''&&sUri!=_sInitUri)||
		_sInitRssCfg!=sRssCfgBinary||_iInitRssCfgNumEntries!=sRssCfgSelIdx){
		_sLastPreviewUri=sUri;
		var sTbBinary=getBinary('tb');
		opener.rpc(escape(sUri),sRssCfgBinary,sRssCfgSelIdx,sTbBinary,getTbPersTitle(sUri),_sObjId);
	}
}

function resetRssFeed(){
	var iSctConfSel=_fo.elements['sct_conf'].selectedIndex;
	var iRdoTitleSel=(_fo.elements['rdo_title'].checked)?0:1;
	if((_sLastPreviewUri!=''&&_sInitUri!=_sLastPreviewUri)||
		(getBinary('conf')!=_sInitRssCfg)||
		(getBinary('tb')!=_sInitTbCfg)||
		(_iInitRssCfgNumEntries!=iSctConfSel)||
		(_iInitTbTitlePers!=iRdoTitleSel)){
		opener.rpc(escape(_sInitUri),_sInitRssCfg,_iInitRssCfgNumEntries,_sInitTbCfg,getTbPersTitle(_sInitUri),_sObjId);
	}
}

function getBinary(postfix){
	var sBinary='';
	var oChbx=_fo.elements['chbx_'+postfix];
	var iChbxLen=oChbx.length;
	for(var i=0;i<iChbxLen;i++){
		sBinary+=(oChbx[i].checked)?'1':'0';
	}
	return sBinary;
}

function save(){
	var debug='';
	var oIptUri=_fo.elements['ipt_uri'];
	var sUri=oIptUri.value;
	if(!isUrl(sUri)){
		//" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[invalid_url]'), we_message_reporting::WE_MESSAGE_ERROR) . "
		//return;
	}
	var oSctConf=_fo.elements['sct_conf'];
	var oCsv_=opener.gel(_sObjId+'_csv');
	var oRdoTitle=_fo.elements['rdo_title'];
	oCsv_.value=opener.base64_encode(sUri)+','+getBinary('conf')+','+oSctConf.selectedIndex+
		','+getBinary('tb')+','+((oRdoTitle[0].checked)?0:1);
	if(_bIsHotTopRssFeeds){
		var oSctRss=_fo.elements['sct_rss'];
		var aNewTopRssFeeds=new Array();
		for(var i=0;_iTopRssFeedsLen>i;i++){
			aNewTopRssFeeds[i]=[opener.base64_encode(oSctRss.options[i+1].text),
				opener.base64_encode(oSctRss.options[i+1].value)];
		}
		opener._trf=aNewTopRssFeeds;
		opener._isHotTrf=true;
	}
	opener.saveSettings();
	savePrefs();
	displayRssFeed(sUri,true);
	" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[prefs_saved_successfully]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
	opener.top.weNavigationHistory.navigateReload();
	self.close();
}

function preview(){
	var oIptUri=_fo.elements['ipt_uri'];
	var sUri=oIptUri.value;
	if(!isUrl(sUri)){
		" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[invalid_url]'), we_message_reporting::WE_MESSAGE_ERROR) . "
		//return;
	}
	previewPrefs();
	displayRssFeed(sUri,false);
}

function exit_close(){
	resetRssFeed();
	exitPrefs();
	self.close();
}
";

function htmlClipElement($smalltext, $text, $content){
	$unique = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(microtime())
	$js = we_html_element::jsElement(
			'var state_' . $unique . '=0;
		function clip_' . $unique . '(){
			var text_' . $unique . '="' . addslashes($text) . '";
			var textsmall_' . $unique . ' = "' . addslashes($smalltext) . '";
			var oText=gel("' . $unique . '");
			var oDiv=gel("div_' . $unique . '");
			var oBtn=gel("btn_' . $unique . '");
			if(state_' . $unique . '==0){
				oText.innerHTML=text_' . $unique . ';
				oDiv.style.display=\'block\';
				oBtn.innerHTML=\'' . we_html_element::htmlA(
				array(
				"href" => "javascript:clip_" . $unique . "();"
				), we_html_element::htmlImg(array(
					"src" => BUTTONS_DIR . "btn_direction_down.gif", "border" => 0
				))) . '\';
				state_' . $unique . '=1;
			}else{
				oText.innerHTML=textsmall_' . $unique . ';
				oDiv.style.display=\'none\';
				oBtn.innerHTML=\'' . we_html_element::htmlA(
				array(
				"href" => "javascript:clip_" . $unique . "();"
				), we_html_element::htmlImg(array(
					"src" => BUTTONS_DIR . "btn_direction_right.gif", "border" => 0
				))) . '\';
				state_' . $unique . '=0;
			}
		}
	');

	$oClip = new we_html_table(array(
			"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
			), 1, 3);
	$oClip->setCol(
		0, 0, array(
		"width" => 21, "valign" => "top", "align" => "right", "id" => "btn_" . $unique
		), we_html_element::htmlA(array(
			"href" => "javascript:clip_" . $unique . "();"
			), we_html_element::htmlImg(array(
				"src" => BUTTONS_DIR . "btn_direction_right.gif", "border" => 0
			))));
	$oClip->setCol(0, 1, array(
		"width" => 10, "nowrap" => "nowrap"
		), we_html_tools::getPixel(10, 1));
	$oClip->setCol(
		0, 2, null, we_html_element::htmlSpan(
			array(
			"id" => $unique,
			"class" => "defaultfont",
			"style" => "cursor:pointer;",
			"onClick" => "clip_" . $unique . "();"
			), addslashes($smalltext)));

	return $js . $oClip->getHTML() . we_html_element::htmlDiv(array(
			"id" => "div_" . $unique, "style" => "display:none;"
			), we_html_tools::getPixel(1, 15) . we_html_element::htmlBr() . $content);
}

$oIptUri = we_html_tools::htmlFormElementTable(
		we_html_tools::htmlTextInput("ipt_uri", 55, "", 255, "title=\"\"", "text", 380, 0), g_l('cockpit', '[url]'), "left", "defaultfont");

$oSctRss = new we_html_select(
		array(
			"name" => "sct_rss", "size" => "1", "class" => "defaultfont", "onChange" => "onChangeSctRss(this);"
	));
$oSctRss->insertOption(0, "", "");
$oTblSctRss = we_html_tools::htmlFormElementTable($oSctRss->getHTML(), g_l('cockpit', '[rss_top_feeds]'), "left", "defaultfont");

$oRemTopFeeds = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_edit_rem]'), 2, 380);
$oIptNewTitle = we_html_tools::htmlFormElementTable(
		we_html_tools::htmlTextInput(
			$name = "ipt_newTitle", $size = 55, $value = "", $maxlength = 255, $attribs = "", $type = "text", $width = 380, $height = 0), g_l('cockpit', '[title]'), "left", "defaultfont");
$oIptNewUri = we_html_tools::htmlFormElementTable(
		we_html_tools::htmlTextInput(
			$name = "ipt_newUri", $size = 55, $value = "", $maxlength = 255, $attribs = "", $type = "text", $width = 380, $height = 0), g_l('cockpit', '[url]'), "left", "defaultfont");
$btnAddTopRssFeed = we_button::create_button(
		"add", "javascript:handleTopRssFeed('add');", false, -1, -1, "", "", false, false);
$btnOverwriteTopRssFeed = we_button::create_button(
		"overwrite", "javascript:handleTopRssFeed('overwrite');", false, -1, -1, "", "", false, false);
$btnDeleteTopRssFeed = we_button::create_button(
		"delete", "javascript:handleTopRssFeed('delete');", false, -1, -1, "", "", false, false);

$oBtnNewFeed = new we_html_table(array(
		"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
		), 1, 5);
$oBtnNewFeed->setCol(0, 0, null, $btnAddTopRssFeed);
$oBtnNewFeed->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
$oBtnNewFeed->setCol(0, 2, null, $btnOverwriteTopRssFeed);
$oBtnNewFeed->setCol(0, 3, null, we_html_tools::getPixel(10, 1));
$oBtnNewFeed->setCol(0, 4, null, $btnDeleteTopRssFeed);

$oNewFeed = new we_html_table(array(
		"width" => 390, "border" => "0", "cellpadding" => "0", "cellspacing" => "0"
		), 3, 1);
$oNewFeed->setCol(
	0, 0, null, $oRemTopFeeds . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . $oIptNewTitle . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . $oIptNewUri);
$oNewFeed->setCol(1, 0, null, we_html_tools::getPixel(1, 5));
$oNewFeed->setCol(2, 0, array(
	"align" => "right"
	), $oBtnNewFeed->getHTML());

$rssUri = $oIptUri . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . $oTblSctRss . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . htmlClipElement(
		g_l('cockpit', '[show_edit_toprssfeeds]'), g_l('cockpit', '[hide_edit_toprssfeeds]'), $oNewFeed->getHTML());

$oRemRssConf = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_content_rem]'), 2, 410);
$oChbxContTitle = we_forms::checkbox($value = 0, $checked = 0, $name = "chbx_conf", $text = g_l('cockpit', '[title]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxContLink = we_forms::checkbox($value = 0, $checked = 0, $name = "chbx_conf", $text = g_l('cockpit', '[link]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxContDesc = we_forms::checkbox($value = 0, $checked = 0, $name = "chbx_conf", $text = g_l('cockpit', '[desc]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxContEnc = we_forms::checkbox($value = 0, $checked = 0, $name = "chbx_conf", $text = g_l('cockpit', '[content_encoded]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxContPubDate = we_forms::checkbox($value = 0, $checked = 0, $name = "chbx_conf", $text = g_l('cockpit', '[pubdate]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxContCategory = we_forms::checkbox($value = 0, $checked = 0, $name = "chbx_conf", $text = g_l('cockpit', '[category]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oSctNumEntries = new we_html_select(array("name" => "sct_conf", "size" => "1", "class" => "defaultfont"));
$oSctNumEntries->insertOption(0, 0, g_l('cockpit', '[no]'));

for($iCurrEntry = 1; $iCurrEntry <= 50; $iCurrEntry++){
	$oSctNumEntries->insertOption($iCurrEntry, $iCurrEntry, $iCurrEntry);
	if($iCurrEntry >= 10){
		$iCurrEntry += ($iCurrEntry == 25) ? 24 : 4;
	}
}

$oRssContR = new we_html_table(array(
		"height" => "100%", "border" => "0", "cellpadding" => "0", "cellspacing" => "0"
		), 2, 3);
$oRssContR->setCol(0, 0, array(
	"valign" => "middle", "class" => "defaultfont"
	), g_l('cockpit', '[limit_entries]'));
$oRssContR->setCol(0, 1, null, we_html_tools::getPixel(5, 1));
$oRssContR->setCol(0, 2, array(
	"valign" => "middle"
	), $oSctNumEntries->getHTML());
$oRssContR->setCol(1, 0, array(
	"colspan" => 3, "valign" => "bottom"
	), $oChbxContPubDate . $oChbxContCategory);

$oSelectRssCont = new we_html_table(array(
		"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
		), 1, 2);
$oSelectRssCont->setCol(0, 0, array(
	"width" => 165
	), $oChbxContTitle . $oChbxContLink . $oChbxContDesc . $oChbxContEnc);
$oSelectRssCont->setCol(0, 1, array(
	"height" => "100%", "valign" => "top"
	), $oRssContR->getHTML());

$rssConf = $oRemRssConf . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . htmlClipElement(
		g_l('cockpit', '[show_select_rsscontent]'), g_l('cockpit', '[hide_select_rsscontent]'), $oSelectRssCont->getHTML());

$oRemLabel = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_label_rem]'), 2, 410);
$oChbxTb[0] = we_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[label_rssfeed]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[1] = we_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[title]'), $uniqid = true, $class = "defaultfont", $onClick = "onDisableRdoGroup('title');", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[2] = we_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[desc]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[3] = we_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[link_url]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[4] = we_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[pubdate]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[5] = we_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[copyright]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oRdoTitle[0] = we_forms::radiobutton($value = 1, $checked = 0, $name = "rdo_title", $text = g_l('cockpit', '[original_of_rssfeed]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoTitle[1] = we_forms::radiobutton($value = 0, $checked = 0, $name = "rdo_title", $text = g_l('cockpit', '[personalized]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");

$oTitleTb = new we_html_table(array(
		"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
		), 2, 1);
$oTitleTb->setCol(0, 0, array(
	"width" => 165
	), $oRdoTitle[0]);
$oTitleTb->setCol(1, 0, array(
	"width" => 165
	), $oRdoTitle[1]);

$oEditTb = new we_html_table(array(
		"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
		), 6, 2);
$oEditTb->setCol(0, 0, array(
	"width" => 165
	), $oChbxTb[0]);
$oEditTb->setCol(1, 0, array(
	"width" => 165, "valign" => "top"
	), $oChbxTb[1]);
$oEditTb->setCol(1, 1, array(
	"width" => 165
	), $oTitleTb->getHTML());
$oEditTb->setCol(2, 0, array(
	"width" => 165
	), $oChbxTb[2]);
$oEditTb->setCol(3, 0, array(
	"width" => 165
	), $oChbxTb[3]);
$oEditTb->setCol(4, 0, array(
	"width" => 165
	), $oChbxTb[4]);
$oEditTb->setCol(5, 0, array(
	"width" => 165
	), $oChbxTb[5]);

$rssLabel = $oRemLabel . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . htmlClipElement(
		g_l('cockpit', '[show_edit_titlebar]'), g_l('cockpit', '[hide_edit_titlebar]'), $oEditTb->getHTML());

$parts = array(
	array(
		"headline" => "", "html" => $rssUri, "space" => 0
	),
	array(
		"headline" => "", "html" => $rssConf, "space" => 0
	),
	array(
		"headline" => "", "html" => $rssLabel, "space" => 0
	),
	array(
		"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_button::create_button('save', 'javascript:save();', false, -1, -1);
$preview_button = we_button::create_button('preview', 'javascript:preview();', false, -1, -1);
$cancel_button = we_button::create_button('close', 'javascript:exit_close();');
$buttons = we_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_multiIconBox::getHTML('rssProps', '100%', $parts, 30, $buttons, -1, '', '', '', g_l('cockpit', '[rss_feed]'), '', 439);

print we_html_element::htmlDocType() .
	we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[rss_feed]')) .
			STYLESHEET .
			we_html_element::cssElement('select{border:#AAAAAA solid 1px}') .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
			we_html_element::jsElement($jsPrefs . $jsCode . we_button::create_state_changer(false))
		) .
		we_html_element::htmlBody(
			array('class' => 'weDialogBody', 'onload' => 'init();'), we_html_element::htmlForm("", $sTblWidget)
		));
