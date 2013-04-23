<?php

/**
 * webEdition CMS
 *
 * $Rev: 5059 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 12:01:25 +0100 (Sun, 04 Nov 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");
we_html_tools::protect();

$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR. 'we_catSelect.php';

$fs = new we_catSelector(
		isset($id) ? $id : ( isset($_REQUEST["id"]) ? $_REQUEST["id"] : ''),
		isset($table) ? $table : ( isset($_REQUEST["table"]) ? $_REQUEST["table"] : FILE_TABLE ),
		isset($JSIDName) ? $JSIDName : ( isset($_REQUEST["JSIDName"]) ? $_REQUEST["JSIDName"] : "" ),
		isset($JSTextName) ? $JSTextName : ( isset($_REQUEST["JSTextName"]) ? $_REQUEST["JSTextName"] : "" ),
		isset($JSCommand) ? $JSCommand : ( isset($_REQUEST["JSCommand"]) ? $_REQUEST["JSCommand"] : "" ),
		isset($order) ? $order : ( isset($_REQUEST["order"]) ? $_REQUEST["order"] : "" ),
		isset($sessionID) ? $sessionID : ( isset($_REQUEST["sessionID"]) ? $_REQUEST["sessionID"] : "" ),
		isset($we_editCatID) ? $we_editCatID : ( isset($_REQUEST["we_editCatID"]) ? $_REQUEST["we_editCatID"] : "" ),
		isset($we_EntryText) ? $we_EntryText : ( isset($_REQUEST["we_EntryText"]) ? $_REQUEST["we_EntryText"] : "" ),
		isset($rootDirID) ? $rootDirID : ( isset($_REQUEST["rootDirID"]) ? $_REQUEST["rootDirID"] : "" ),
		isset($noChoose) ? $noChoose : ( isset($_REQUEST["noChoose"]) ? $_REQUEST["noChoose"] : "" ));

$fs->printHTML(isset($_REQUEST["what"]) ? $_REQUEST["what"] : we_fileselector::FRAMESET);
