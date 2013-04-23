<?php

/**
 * webEdition CMS
 *
 * $Rev: 4993 $
 * $Author: mokraemer $
 * $Date: 2012-10-14 20:15:05 +0200 (Sun, 14 Oct 2012) $
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
$aProps = array(
	0,
	0,
	0,
	$_REQUEST['we_cmd'][0]
);
require_once('../../mod/upb.php');

$sTb = ($bTypeDoc && $bTypeObj) ? g_l('cockpit', "[upb_docs_and_objs]") : (($bTypeDoc) ? g_l('cockpit', "[upb_docs]") : (($bTypeObj) ? g_l('cockpit', "[upb_objs]") : g_l('cockpit', "[upb_docs_and_objs]")));

$jsCode = "
var _sObjId='" . $_REQUEST['we_cmd'][5] . "';
var _sType='upb';
var _sTb='" . $sTb . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}
";

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[unpublished]')) . STYLESHEET . we_html_element::jsElement(
				$jsCode)) . we_html_element::htmlBody(
			array(
			"marginwidth" => "15",
			"marginheight" => "10",
			"leftmargin" => "15",
			"topmargin" => "10",
			"onload" => "if(parent!=self)init();"
			), we_html_element::htmlDiv(array(
				"id" => "upb"
				), $ct)));