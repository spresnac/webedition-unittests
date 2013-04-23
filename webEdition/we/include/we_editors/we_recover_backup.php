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

if(isset($_REQUEST["pnt"]))
	$what = $_REQUEST["pnt"];
else
	$what = "frameset";

if(isset($_REQUEST["step"]))
	$step = $_REQUEST["step"];
else
	$step = 1;

$weBackupWizard = new weBackupWizard(WE_INCLUDES_DIR . "we_editors/we_recover_backup.php", weBackupWizard::RECOVER);

switch($what){
	case "frameset": print $weBackupWizard->getHTMLFrameset();
		break;
	case "body": print $weBackupWizard->getHTMLStep($step);
		break;
	case "cmd": print $weBackupWizard->getHTMLCmd();
		break;
	case "busy": print $weBackupWizard->getHTMLBusy();
		break;
	case "extern": print $weBackupWizard->getHTMLExtern();
		break;
	case "checker": print $weBackupWizard->getHTMLChecker();
		break;
	default:
		t_e(__FILE__ . " unknown reference: $what");
}

