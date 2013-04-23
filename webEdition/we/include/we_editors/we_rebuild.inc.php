<?php

/**
 * webEdition CMS
 *
 * $Rev: 5000 $
 * $Author: mokraemer $
 * $Date: 2012-10-18 23:35:06 +0200 (Thu, 18 Oct 2012) $
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

$fr = isset($_REQUEST["fr"]) ? $_REQUEST["fr"] : "";


switch($fr){
	case "body":
		print we_rebuild_wizard::getBody();
		break;
	case "busy":
		print we_rebuild_wizard::getBusy();
		break;
	case "cmd":
		print we_rebuild_wizard::getCmd();
		break;
	default:
		print we_rebuild_wizard::getFrameset();
}

