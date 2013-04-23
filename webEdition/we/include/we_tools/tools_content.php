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

$tools = weToolLookup::getAllTools(true, true);

$whiteList = array();
foreach($tools as $k => $v){
	if(isset($v['name'])){
		$whiteList[] = $v['name'];
	}
}

if(!isset($_REQUEST['tool']) || $_REQUEST['tool'] == '' || !in_array($_REQUEST['tool'], $whiteList)){
	exit();
}

//check if bootstrap file exists of specific app
if(file_exists(WEBEDITION_PATH. 'apps/' . $_REQUEST['tool'] . '/index.php')){

	header('Location: ' . WEBEDITION_DIR . 'apps/' . $_REQUEST['tool'] . '/index.php/frameset/index' .
		(isset($REQUEST['modelid']) ? '/modelId/' . intval($REQUEST['modelid']) : '') .
		(isset($REQUEST['tab']) ? '/tab/' . intval($REQUEST['tab']) : ''));
	exit();
}
if($_REQUEST['tool'] == 'weSearch' || $_REQUEST['tool'] == 'navigation'){
	include_once(WE_INCLUDES_PATH . 'we_tools/' . $_REQUEST['tool'] . '/edit_' . $_REQUEST['tool'] . '_frameset.php');
}
