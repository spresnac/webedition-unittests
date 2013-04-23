<?php

/**
 * webEdition CMS
 *
 * $Rev: 4227 $
 * $Author: mokraemer $
 * $Date: 2012-03-09 00:11:56 +0100 (Fri, 09 Mar 2012) $
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
we_html_tools::htmlTop();

print STYLESHEET;

$workflowFrame = new we_workflow_frames();
$workflowFrame->View->processVariables();
$workflowFrame->View->processCommands();

$what = (isset($_GET["pnt"]) ? $_GET["pnt"] : "frameset");
$mode = (isset($_GET["art"]) ? $_GET["art"] : 0);
$type = (isset($_GET["type"]) ? $_GET["type"] : 0);

switch($what){
	case "frameset":
		print $workflowFrame->getHTMLFrameset();
		break;

	case "header":
		print $workflowFrame->getHTMLHeader();
		break;

	case "resize":
		print $workflowFrame->getHTMLResize();
		break;

	case "left":
		print $workflowFrame->getHTMLLeft();
		break;
	case "right":
		print $workflowFrame->getHTMLRight();
		break;

	case "editor":
		print $workflowFrame->getHTMLEditor();
		break;

	case "edheader":
		print $workflowFrame->getHTMLEditorHeader($mode);
		break;

	case "edbody":
		print $workflowFrame->getHTMLEditorBody();
		break;

	case "edfooter":
		print $workflowFrame->getHTMLEditorFooter($mode);
		break;

	case "qlog":
		print $workflowFrame->getHTMLLogQuestion();
		break;

	case "log":
		print $workflowFrame->getHTMLLog($mode, $type);
		break;

	case "cmd":
		print $workflowFrame->getHTMLCmd();
		break;

	default:
}
