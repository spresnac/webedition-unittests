<?php

/**
 * webEdition CMS
 *
 * $Rev: 5101 $
 * $Author: mokraemer $
 * $Date: 2012-11-08 16:19:49 +0100 (Thu, 08 Nov 2012) $
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

$we_transaction = $_REQUEST['we_cmd'][3];
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : '');

$nr = abs($_REQUEST['we_cmd'][2]);

$GLOBALS['we_doc'] = new we_template();
$GLOBALS['we_doc']->Table = TEMPLATES_TABLE;
$GLOBALS['we_doc']->we_new();

$filename = "we_" . $_REQUEST["SID"] . "_Filename";
$filename = $_REQUEST[$filename];

$ParentID = "we_" . $_REQUEST["SID"] . "_ParentID";
$ParentID = $_REQUEST[$ParentID];


$GLOBALS['we_doc']->Filename = $filename;
$GLOBALS['we_doc']->Extension = ".tmpl";
$GLOBALS['we_doc']->Icon = "prog.gif";
$GLOBALS['we_doc']->setParentID($ParentID);
$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->ParentPath . (($GLOBALS['we_doc']->ParentPath != "/") ? "/" : "") . $filename . ".tmpl";
$GLOBALS['we_doc']->ContentType = "text/weTmpl";

$GLOBALS['we_doc']->Table = TEMPLATES_TABLE;


//$GLOBALS['we_doc']->ID = 61;
//  $_SESSION["content"] is only used for generating a default template, it is
//  set in WE_OBJECT_MODULE_PATH\we_object_createTemplate.inc.php
$GLOBALS['we_doc']->elements["data"]["dat"] = $_SESSION['weS']['content'];
$GLOBALS['we_doc']->elements["data"]["type"] = "txt";
unset($_SESSION['weS']['content']);

if($GLOBALS['we_doc']->i_filenameEmpty()){
	$we_responseText = g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][filename_empty]');
} else if($GLOBALS['we_doc']->i_sameAsParent()){
	$we_responseText = g_l('weEditor', "[folder_save_nok_parent_same]");
} else if($GLOBALS['we_doc']->i_filenameNotValid()){
	$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][we_filename_notValid]'), $GLOBALS['we_doc']->Path);
} else if($GLOBALS['we_doc']->i_filenameNotAllowed()){
	$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][we_filename_notAllowed]'), $GLOBALS['we_doc']->Path);
} else if($GLOBALS['we_doc']->i_filenameDouble()){
	$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_path_exists]'), $GLOBALS['we_doc']->Path);
}
if(isset($we_responseText)){
	echo we_html_element::jsElement(we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR));
	include_once(WE_OBJECT_MODULE_PATH . "we_object_createTemplate.inc.php");
} else{
	if($GLOBALS['we_doc']->we_save()){
		$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_save_ok]'), $GLOBALS['we_doc']->Path);
		echo we_html_element::jsElement(we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_NOTICE, false, true) . '
opener.we_cmd("changeTempl_ob",' . $nr . ',' . $GLOBALS['we_doc']->ID . ');
self.close();');
	} else{
		$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_save_notok]'), $GLOBALS['we_doc']->Path);
		echo we_html_element::jsElement(we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR));
		include_once(WE_OBJECT_MODULE_PATH . 'we_object_createTemplate.inc.php');
	}
}
