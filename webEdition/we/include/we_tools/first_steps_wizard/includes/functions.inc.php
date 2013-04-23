<?php

/**
 * webEdition CMS
 *
 * $Rev: 4678 $
 * $Author: mokraemer $
 * $Date: 2012-07-07 02:28:36 +0200 (Sat, 07 Jul 2012) $
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
function we_loadDefaultMasterTemplateConfig(){

	$file = $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tools/first_steps_wizard/conf/defaultMasterTemplate.inc.php";
	if(!file_exists($file) || !is_file($file)){
		we_writeDefaultMasterTemplateConfig(0);
	}
	include_once ($file);
}

function we_writeDefaultMasterTemplateConfig($default){

	$code = <<<EOF
<?php

// Default Master Template for the First Steps Wizard
define("FSW_DEFAULT_MASTER_TEMPLATE", {$default});

?>
EOF;

	$file = $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tools/first_steps_wizard/conf/defaultMasterTemplate.inc.php";
	return weFile::save($file,$code,'w+');
}