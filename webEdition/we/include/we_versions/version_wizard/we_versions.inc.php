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
we_html_tools::protect();

include_once (WE_INCLUDES_PATH . 'we_versions/version_wizard/we_versions_wizard.inc.php');

$fr = isset($_REQUEST["fr"]) ? $_REQUEST["fr"] : "";

switch($fr){

	case "body" :
		print we_versions_wizard::getBody();
		break;
	case "busy" :
		print we_versions_wizard::getBusy();
		break;
	case "cmd" :
		print we_versions_wizard::getCmd();
		break;
	default :
		print we_versions_wizard::getFrameset();
}
