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
we_html_tools::protect();
$_SERVER["SCRIPT_NAME"] = WE_MODULES_DIR . "export/we_exportDirSelect.php";
$fs = new we_exportDirSelector(isset($id) ? $id : (isset($_REQUEST["id"]) ? $_REQUEST["id"] : ''),
		isset($JSIDName) ? $JSIDName : (isset($_REQUEST["JSIDName"]) ? $_REQUEST["JSIDName"] : ''),
		isset($JSTextName) ? $JSTextName : (isset($_REQUEST["JSTextName"]) ? $_REQUEST["JSTextName"] : ''),
		isset($JSCommand) ? $JSCommand : (isset($_REQUEST["JSCommand"]) ? $_REQUEST["JSCommand"] : ''),
		isset($order) ? $order : (isset($_REQUEST["order"]) ? $_REQUEST["order"] : ''),
		isset($we_editDirID) ? $we_editDirID : (isset($_REQUEST["we_editDirID"]) ? $_REQUEST["we_editDirID"] : ''),
		isset($we_FolderText) ? $we_FolderText : (isset($_REQUEST["we_FolderText"]) ? $_REQUEST["we_FolderText"] : ''));

$fs->printHTML(isset($_REQUEST["what"]) ? $_REQUEST["what"] : we_fileselector::FRAMESET);