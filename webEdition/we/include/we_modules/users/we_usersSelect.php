<?php

/**
 * webEdition CMS
 *
 * $Rev: 4705 $
 * $Author: arminschulz $
 * $Date: 2012-07-15 10:59:02 +0200 (Sun, 15 Jul 2012) $
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
$_SERVER["SCRIPT_NAME"] = WE_USERS_MODULE_DIR . "we_usersSelect.php";

$fs = new we_usersSelector(isset($id) ? $id : (isset($_REQUEST["id"]) ? $_REQUEST["id"] : "" ),
		isset($table) ? $table : (isset($_REQUEST["table"]) ? $_REQUEST["table"] : USER_TABLE),
		isset($JSIDName) ? $JSIDName : (isset($_REQUEST["JSIDName"]) ? $_REQUEST["JSIDName"] : "" ),
		isset($JSTextName) ? $JSTextName : (isset($_REQUEST["JSTextName"]) ? $_REQUEST["JSTextName"] : "" ),
		isset($JSCommand) ? $JSCommand : (isset($_REQUEST["JSCommand"]) ? $_REQUEST["JSCommand"] : "" ),
		isset($order) ? $order : (isset($_REQUEST["order"]) ? $_REQUEST["order"] : "" ),
		isset($sessionID) ? $sessionID : (isset($_REQUEST["sessionID"]) ? $_REQUEST["sessionID"] : "" ),
		isset($rootDirID) ? $rootDirID : (isset($_REQUEST["rootDirID"]) ? $_REQUEST["rootDirID"] : "" ),
		isset($filter) ? $filter : (isset($_REQUEST["filter"]) ? $_REQUEST["filter"] : "" ),
		isset($multiple) ? $multiple : (isset($_REQUEST["multiple"]) ? $_REQUEST["multiple"] : "" ));

$fs->printHTML(isset($_REQUEST["what"]) ? $_REQUEST["what"] : we_fileselector::FRAMESET);
