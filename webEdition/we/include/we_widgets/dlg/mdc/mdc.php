<?php

/**
 * webEdition CMS
 *
 * $Rev: 4992 $
 * $Author: mokraemer $
 * $Date: 2012-10-14 19:50:24 +0200 (Sun, 14 Oct 2012) $
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
// widget MY DOCUMENTS

list($dir, $dt_tid, $cats) = explode(";", $_REQUEST['we_cmd'][1]);
$aCsv = array(
	0, //unused - compatibility
	$_REQUEST['we_cmd'][0],
	$dir,
	$dt_tid,
	$cats
);
require_once('../../mod/mdc.php');

$js = "
var _sObjId='" . $_REQUEST['we_cmd'][5] . "';
var _sType='mdc';
var _sTb='" . ($_REQUEST['we_cmd'][4] == "" ? (($_binary{1}) ? g_l('cockpit', '[my_objects]') : g_l('cockpit', '[my_documents]')) : $_REQUEST['we_cmd'][4]) . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}
";

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[my_documents]')) . STYLESHEET . we_html_element::jsElement(
				$js)) . we_html_element::htmlBody(
			array(
			"marginwidth" => "15",
			"marginheight" => "10",
			"leftmargin" => "15",
			"topmargin" => "10",
			"onload" => "if(parent!=self)init();"
			), we_html_element::htmlDiv(array(
				"id" => "mdc"
				), $mdc)));


