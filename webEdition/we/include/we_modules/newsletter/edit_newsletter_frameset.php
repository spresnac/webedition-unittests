<?php

/**
 * webEdition CMS
 *
 * $Rev: 4660 $
 * $Author: mokraemer $
 * $Date: 2012-07-04 23:23:06 +0200 (Wed, 04 Jul 2012) $
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

$what = (isset($_REQUEST["pnt"]) ? $_REQUEST["pnt"] : 'frameset');
$mode = (isset($_REQUEST["art"]) ? $_REQUEST["art"] : 0);


if($what != "send" && $what != "send_body" && $what != "send_cmd" && $what != "edbody" && $what != "preview" && $what != "black_list" && $what != "newsletter_settings" && $what != "eemail" && $what != "edit_file" && $what != "clear_log" && $what != "export_csv_mes" && $what != "qsend" && $what != "qsave1"){
	we_html_tools::htmlTop();
	print STYLESHEET;
}

$newsletterFrame = new weNewsletterFrames();

if(isset($_REQUEST["inid"])){
	$newsletterFrame->View->newsletter = new weNewsletter($_REQUEST["inid"]);
} else{
	switch($what){
		case "export_csv_mes":
		case "newsletter_settings":
		case "qsend":
		case "eedit":
		case "black_list":
		case "upload_csv":
			break;
		default:
			$newsletterFrame->View->processVariables();
	}
}

switch($what){
	case "export_csv_mes":
	case "preview":
	case "domain_check":
	case "newsletter_settings":
	case "show_log":
	case "print_lists":
	case "qsend":
	case "eedit":
	case "black_list":
		break;
	default:
		$newsletterFrame->View->processCommands();
}

switch($what){
	case "frameset":
		print $newsletterFrame->getHTMLFrameset();
		break;

	case "header":
		print $newsletterFrame->getHTMLHeader();
		break;

	case "resize":
		print $newsletterFrame->getHTMLResize();
		break;

	case "left":
		print $newsletterFrame->getHTMLLeft();
		break;

	case "right":
		print $newsletterFrame->getHTMLRight();
		break;

	case "editor":
		print $newsletterFrame->getHTMLEditor();
		break;

	case "edheader":
		print $newsletterFrame->getHTMLEditorHeader($mode);
		break;

	case "edbody":
		print $newsletterFrame->getHTMLEditorBody();
		break;

	case "edfooter":
		print $newsletterFrame->getHTMLEditorFooter($mode);
		break;

	case "qlog":
		print $newsletterFrame->getHTMLLogQuestion();
		break;

	case "domain_check":
		print $newsletterFrame->getHTMLDCheck();
		break;

	case "show_log":
		print $newsletterFrame->getHTMLLog();
		break;

	case "newsletter_settings":
		print $newsletterFrame->getHTMLSettings();
		break;

	case "print_lists":
		print $newsletterFrame->getHTMLPrintLists();
		break;

	case "cmd":
		print $newsletterFrame->getHTMLCmd();
		break;

	case "qsend":
		print $newsletterFrame->getHTMLSendQuestion();
		break;

	case "qsave1":
		print $newsletterFrame->getHTMLSaveQuestion1();
		break;

	case "eemail":
		print $newsletterFrame->getHTMLEmailEdit();
		break;

	case "preview":
		print $newsletterFrame->getHTMLPreview();
		break;

	case "black_list":
		print $newsletterFrame->getHTMLBlackList();
		break;

	case "upload_black":
		print $newsletterFrame->getHTMLUploadCsv("javascript:we_cmd('do_upload_black');");
		break;

	case "upload_csv":
		print $newsletterFrame->getHTMLUploadCsv();
		break;

	case "export_csv_mes":
		print $newsletterFrame->getHTMLExportCsvMessage();
		break;

	case "edit_file":
		print $newsletterFrame->getHTMLEditFile($mode);
		break;

	case "clear_log":
		print $newsletterFrame->getHTMLClearLog();
		break;

	case "send":
		print $newsletterFrame->getHTMLSendWait();
		break;

	case "send_frameset":
		print $newsletterFrame->getHTMLSendFrameset();
		break;

	case "send_body":
		print $newsletterFrame->getHTMLSendBody();
		break;

	case "send_cmd":
		print $newsletterFrame->getHTMLSendCmd();
		break;
	case "send_control":
		print $newsletterFrame->getHTMLSendControl();
		break;

	default:
}