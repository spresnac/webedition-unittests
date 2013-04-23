<?php
/**
 * webEdition CMS
 *
 * $Rev: 3695 $
 * $Author: mokraemer $
 * $Date: 2012-01-01 18:33:26 +0100 (Sun, 01 Jan 2012) $
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

$jsPrefs = "
var _sObjId='" . $_REQUEST['we_cmd'][0] . "';
var _sCls_=opener.gel(_sObjId+'_cls').value;
var _fo,_sInitCls,_oSctCls;
var _iInitCls=0;

function initPrefs(){
	_oSctCls=_fo.elements['sct_cls'];
	var iSctClsLen=_oSctCls.length;
	_sInitCls=_sCls_;
	for(var i=iSctClsLen-1;i>=0;i--){
		if(_oSctCls.options[i].value==_sCls_){
			_oSctCls.options[i].selected=true;
			_iInitCls=i;
		}
	}
}

function savePrefs(){
	opener.setTheme(_sObjId,_oSctCls[_oSctCls.selectedIndex].value);
}

function previewPrefs(){
	opener.setTheme(_sObjId,_oSctCls[_oSctCls.selectedIndex].value);
}

function exitPrefs(){
	var sTheme=_oSctCls[_oSctCls.selectedIndex].value;
	if(_sCls_!=sTheme){
		sTheme=_oSctCls[_iInitCls].value;
		opener.setTheme(_sObjId,sTheme);
	}
}
";

$oSctCls = new we_html_select(
		array(

				"name" => "sct_cls",
				"size" => "1",
				"class" => "defaultfont",
				"style" => "width:120px;border:#AAAAAA solid 1px"
		));
$oSctCls->insertOption(0, "white", g_l('cockpit','[white]'));
$oSctCls->insertOption(1, "lightCyan", g_l('cockpit','[lightcyan]'));
$oSctCls->insertOption(2, "blue", g_l('cockpit','[blue]'));
$oSctCls->insertOption(3, "green", g_l('cockpit','[green]'));
$oSctCls->insertOption(4, "orange", g_l('cockpit','[orange]'));
$oSctCls->insertOption(5, "yellow", g_l('cockpit','[yellow]'));
$oSctCls->insertOption(6, "red", g_l('cockpit','[red]'));

$oSelCls = new we_html_table(array(
	"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
), 1, 2);
$oSelCls->setCol(0, 0, array(
	"width" => 130, "class" => "defaultfont"
), g_l('cockpit','[bgcolor]'));
$oSelCls->setCol(0, 1, null, $oSctCls->getHTML());