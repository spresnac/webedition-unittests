<?php
/**
 * webEdition CMS
 *
 * $Rev: 3539 $
 * $Author: mokraemer $
 * $Date: 2011-12-11 19:17:49 +0100 (Sun, 11 Dec 2011) $
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

/**
 *
 * Load all necessary files for the wizard
 *
 */

// Some constants for LiveUpdate Functions
if(file_exists($_SERVER['DOCUMENT_ROOT']."/webEdition/liveUpdate/includes/proxysettings.inc.php")){
	include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/liveUpdate/includes/proxysettings.inc.php");

}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/includes/define.inc.php');

// Live Update Classes
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/classes/liveUpdateHttp.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/classes/liveUpdateTemplates.class.php');


// Some constants for Wizard
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/leWizard/includes/define.inc.php');

