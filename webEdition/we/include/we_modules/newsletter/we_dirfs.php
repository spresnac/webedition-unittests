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
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

$id = isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : "";

$JSIDName = stripslashes(we_cmd_dec(2));
$JSTextName = stripslashes(we_cmd_dec(3));
$JSCommand = we_cmd_dec(4);
$sessionID = isset($_REQUEST['we_cmd'][5]) ? $_REQUEST['we_cmd'][5] : "";
$rootDirID = isset($_REQUEST['we_cmd'][6]) ? $_REQUEST['we_cmd'][6] : "";
$filter = isset($_REQUEST['we_cmd'][7]) ? $_REQUEST['we_cmd'][7] : "";
$multiple = isset($_REQUEST['we_cmd'][8]) ? $_REQUEST['we_cmd'][8] : "";

include_once(WE_MODULES_PATH . 'newsletter/we_newsletterDirSelector.php');
