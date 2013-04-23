<?php

/**
 * webEdition CMS
 *
 * $Rev: 4320 $
 * $Author: mokraemer $
 * $Date: 2012-03-23 00:51:46 +0100 (Fri, 23 Mar 2012) $
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
if(!isset($_REQUEST['we_cmd'])){
	exit();
}

$include = "";

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

switch($_REQUEST['we_cmd'][0]){
	case "selectorSuggest" :
		break;
}
if($include){
	include(WE_INCLUDES_PATH . $include);
}