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

$id = $_REQUEST['we_cmd'][4];
$table = USER_TABLE;

$JSIDName = we_cmd_dec(1);
$JSTextName = we_cmd_dec(2);
$JSCommand = we_cmd_dec(5);
$sessionID = isset($_REQUEST['we_cmd'][6]) ? $_REQUEST['we_cmd'][6] : 0;
$rootDirID = isset($_REQUEST['we_cmd'][7]) ? $_REQUEST['we_cmd'][7] : 0;
$filter = $_REQUEST['we_cmd'][3];
$multiple = isset($_REQUEST['we_cmd'][8]) ? $_REQUEST['we_cmd'][8] : 0;

include_once(WE_USERS_MODULE_PATH . "we_usersSelect.php");
