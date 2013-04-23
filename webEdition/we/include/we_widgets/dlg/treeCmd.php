<?php

/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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
if(isset($_REQUEST["cmd"])){
	switch($_REQUEST["cmd"]){
		case "load" :
			if(isset($_REQUEST["pid"])){
				print
					we_html_element::jsElement(
						"self.location='" . WE_INCLUDES_DIR . "we_export/exportLoadTree.php?we_cmd[1]=" . $_REQUEST["tab"] . "&we_cmd[2]=" . $_REQUEST["pid"] . "&we_cmd[3]=" . (isset(
							$_REQUEST["openFolders"]) ? $_REQUEST["openFolders"] : "") . "&we_cmd[4]=top'");
			}
			break;
	}
}
