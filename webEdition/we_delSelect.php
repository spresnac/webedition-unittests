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

$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_delSelect.php';

$fs = new we_delSelector(
		isset($id) ? $id : ( isset($_REQUEST["id"]) ? $_REQUEST["id"] : ''),
		isset($table) ? $table : ( isset($_REQUEST["table"]) ? $_REQUEST["table"] : FILE_TABLE ));

$fs->printHTML(isset($_REQUEST["what"]) ? $_REQUEST["what"] : we_fileselector::FRAMESET);

