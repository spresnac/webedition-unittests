<?php

/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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
$we_transaction = $_REQUEST['we_cmd'][1] ? $_REQUEST['we_cmd'][1] : $we_transaction;
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : '');

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
include( WE_OBJECT_MODULE_PATH . "we_objectFile.inc.php");


we_html_tools::protect();

switch($_REQUEST['we_cmd'][0]){
	case "toggleExtraWorkspace":
		$oid = $_REQUEST['we_cmd'][2];
		$wsid = $_REQUEST['we_cmd'][3];
		$wsPath = id_to_path($wsid, FILE_TABLE, $DB_WE);
		$tableID = $_REQUEST['we_cmd'][4];
		$ofID = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE ObjectID='$oid' AND TableID=" . intval($tableID), "ID", $DB_WE);
		$foo = f("SELECT OF_ExtraWorkspacesSelected FROM " . OBJECT_X_TABLE . intval($tableID) . " WHERE ID='" . $oid . "'", "OF_ExtraWorkspacesSelected", $DB_WE);
		if(strstr($foo, "," . $wsid . ",")){
			$ews = str_replace("," . $wsid, ",", "", $foo);
			if($ews == ",")
				$ews = "";
			$check = 0;
		}
		else{
			$ews = ($foo ? $foo : ",") . $wsid . ",";
			$check = 1;
		}
		$DB_WE->query("UPDATE " . OBJECT_X_TABLE . intval($tableID) . " SET OF_ExtraWorkspacesSelected='" . $DB_WE->escape($ews) . "' WHERE ID=" . intval($oid));
		$DB_WE->query("UPDATE " . OBJECT_FILES_TABLE . " SET ExtraWorkspacesSelected='" . $DB_WE->escape($ews) . "' WHERE ID=" . intval($ofID));
		$of = new we_objectFile();
		$of->initByID($ofID, OBJECT_FILES_TABLE);
		$of->insertAtIndex();
		print we_html_element::jsElement('top.we_cmd("reload_editpage");');
		break;
	case "obj_search":
		$we_doc->Search = $_REQUEST['we_cmd'][2];
		$we_doc->SearchField = $_REQUEST['we_cmd'][3];
		$we_doc->EditPageNr = WE_EDITPAGE_WORKSPACE;
		$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_WORKSPACE;
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		print we_html_element::jsElement('top.we_cmd("switch_edit_page",' . WE_EDITPAGE_WORKSPACE . ',"' . $_REQUEST['we_cmd'][1] . '");');
		break;
}