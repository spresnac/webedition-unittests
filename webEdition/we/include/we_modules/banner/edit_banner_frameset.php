<?php

/**
 * webEdition CMS
 *
 * $Rev: 4040 $
 * $Author: mokraemer $
 * $Date: 2012-02-15 19:24:09 +0100 (Wed, 15 Feb 2012) $
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

$bannerFrame = new weBannerFrames();
$bannerFrame->View->processVariables();
$bannerFrame->View->processCommands();

$what = (isset($_REQUEST["pnt"])) ? $_REQUEST["pnt"] : "frameset";
$mode = (isset($_REQUEST["art"])) ? $_REQUEST["art"] : 0;


switch($what){
	case "frameset": print $bannerFrame->getHTMLFrameset();
		break;
	case "header": print $bannerFrame->getHTMLHeader();
		break;
	case "resize": print $bannerFrame->getHTMLResize();
		break;
	case "left": print $bannerFrame->getHTMLLeft();
		break;
	case "right": print $bannerFrame->getHTMLRight();
		break;
	case "editor": print $bannerFrame->getHTMLEditor();
		break;
	case "edheader": print $bannerFrame->getHTMLEditorHeader($mode);
		break;
	case "edbody": print $bannerFrame->getHTMLEditorBody();
		break;
	case "edfooter": print $bannerFrame->getHTMLEditorFooter($mode);
		break;
	case "cmd": print $bannerFrame->getHTMLCmd();
		break;
	default:
}
