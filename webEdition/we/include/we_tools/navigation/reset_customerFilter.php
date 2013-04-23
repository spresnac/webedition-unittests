<?php

/**
 * webEdition CMS
 *
 * $Rev: 4040 $
 * $Author: mokraemer $
 * $Date: 2012-02-15 19:24:09 +0100 (Wed, 15 Feb 2012) $
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
if($_SESSION["perms"]["ADMINISTRATOR"]){
	$GLOBALS['DB_WE']->query("UPDATE " . NAVIGATION_TABLE . " SET  LimitAccess=0, ApplyFilter=0");

	print we_html_element::jsElement(
			'top.openWindow(\'' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=rebuild&step=2&type=rebuild_navigation&responseText=' . rawurlencode(
				g_l('navigation', '[reset_customerfilter_done_message]')) . '\',\'resave\',-1,-1,600,130,0,true);
');
}