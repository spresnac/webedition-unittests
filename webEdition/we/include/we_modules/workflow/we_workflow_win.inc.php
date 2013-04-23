<?php

/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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

$_REQUEST['we_cmd'] = isset($_REQUEST['we_cmd']) ? $_REQUEST['we_cmd'] : "";
$cmd = isset($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : "";
$we_transaction = isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : (isset($_REQUEST["we_transaction"]) ? $_REQUEST["we_transaction"] : "");
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : 0);

$wf_select = isset($_REQUEST["wf_select"]) ? $_REQUEST["wf_select"] : "";
$wf_text = isset($_REQUEST["wf_select"]) ? $_REQUEST["wf_text"] : "";

###### init document #########
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');


we_html_tools::htmlTop();

switch($_REQUEST['we_cmd'][0]){

	case "in_workflow":
		include(WE_WORKFLOW_MODULE_PATH . "we_in_workflow.inc.php");
		break;
	case "pass":
		include(WE_WORKFLOW_MODULE_PATH . "we_pass_workflow.inc.php");
		break;
	case "decline":
		include(WE_WORKFLOW_MODULE_PATH . "we_decline_workflow.inc.php");
		break;
}
