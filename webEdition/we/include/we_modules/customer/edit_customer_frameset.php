<?php

/**
 * webEdition CMS
 *
 * $Rev: 4673 $
 * $Author: mokraemer $
 * $Date: 2012-07-06 19:32:57 +0200 (Fri, 06 Jul 2012) $
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
$what = isset($_REQUEST["pnt"]) ? $_REQUEST["pnt"] : 'frameset';
$mode = isset($_REQUEST["art"]) ? $_REQUEST["art"] : 0;

//We need to set this (and in corresponding frames, since the data in database is formated this way
if(!($mode == 'export' && isset($_REQUEST["step"]) && $_REQUEST["step"] == 5)){
	we_html_tools::headerCtCharset('text/html', DEFAULT_CHARSET);
	we_html_tools::htmlTop('', DEFAULT_CHARSET);
}

$ExImport = $weFrame = null;

if($what == "export" || $what == "eibody" || $what == "eifooter" || $what == "eiload" || $what == "import" || $what == "eiupload"){
	$ExImport = new weCustomerEIWizard();

	$step = (isset($_REQUEST["step"]) ? $_REQUEST["step"] : 0);
} else{
	$weFrame = new weCustomerFrames();
	$weFrame->View->processVariables();
	$weFrame->View->processCommands();
}

switch($what){
	case "frameset":
		print $weFrame->getHTMLFrameset();
		break;
	case "header":
		print $weFrame->getHTMLHeader();
		break;
	case "resize":
		print $weFrame->getHTMLResize();
		break;
	case "left":
		print $weFrame->getHTMLLeft();
		break;
	case "right":
		print $weFrame->getHTMLRight();
		break;
	case "editor":
		print $weFrame->getHTMLEditor();
		break;
	case "edheader":
		print $weFrame->getHTMLEditorHeader();
		break;
	case "edbody":
		print $weFrame->getHTMLEditorBody();
		break;
	case "edfooter":
		print $weFrame->getHTMLEditorFooter();
		break;
	case "cmd":
		print $weFrame->getHTMLCmd();
		break;
	case "treeheader":
		print $weFrame->getHTMLTreeHeader();
		break;
	case "treefooter":
		print $weFrame->getHTMLTreeFooter();
		break;
	case "customer_admin":
		print $weFrame->getHTMLCustomerAdmin();
		break;
	case "branch_editor":
		print $weFrame->getHTMLFieldEditor("branch", $mode);
		break;
	case "field_editor":
		print $weFrame->getHTMLFieldEditor("field", $mode);
		break;
	case "sort_admin":
		print $weFrame->getHTMLSortEditor();
		break;
	case "search":
		print $weFrame->getHTMLSearch();
		break;
	case "settings":
		print $weFrame->getHTMLSettings();
		break;

	case "export":
	case "import":
		print $ExImport->getHTMLFrameset($what);
		break;
	case "eibody":
		print $ExImport->getHTMLStep($mode, $step);
		break;
	case "eifooter":
		print $ExImport->getHTMLFooter($mode, $step);
		break;
	case "eiload":
		print $ExImport->getHTMLLoad();
		break;

	default:
		error_log(__FILE__ . " unknown reference: $what");
}
