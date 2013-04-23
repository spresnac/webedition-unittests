<?php
/**
 * webEdition CMS
 *
 * $Rev: 3953 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 19:12:45 +0100 (Tue, 07 Feb 2012) $
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

// Load needed files for wizard
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/first_steps_wizard/includes/includes.inc.php');

// Load language file for first steps wizard
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS["WE_LANGUAGE"] . '/tools/first_steps_wizard/master.inc.php');

$WizardCollection = new leWizardCollection('/we/include/we_tools/first_steps_wizard/master_wizards.inc.php');

if (isset($_REQUEST["leWizard"])) {
	print $WizardCollection->executeStep();

} else {
	include ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/first_steps_wizard/includes/template.inc.php');

}