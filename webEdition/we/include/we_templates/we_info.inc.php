<?php

/**
 * webEdition CMS
 *
 * $Rev: 5936 $
 * $Author: lukasimhof $
 * $Date: 2013-03-09 21:19:46 +0100 (Sat, 09 Mar 2013) $
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
//	build table for login screen.
$_widthTotal = 432;
$_space = 15;
$_middlePart = ($_widthTotal - (2 * $_space));

// widths of loginTable
$_logoPart = 140;
$_leftPart = $_middlePart - $_logoPart;

$_credits = '<br /><span style="line-height:160%">' .
	g_l('global', '[developed_further_by]') . ': <a href="http://www.webedition.org/" target="_blank" ><strong>webEdition e.V.</strong></a><br/>' .
	g_l('global', '[with]') . ' <b><a href="http://credits.webedition.org/?language=' . $GLOBALS["WE_LANGUAGE"] . '" target="_blank" >' . g_l('global', '[credits_team]') . '</a></b></span><br/>';

$we_version = '';
if(!isset($GLOBALS['loginpage'])){
	$we_version .= ((defined('WE_VERSION_NAME') && WE_VERSION_NAME != '') ? WE_VERSION_NAME : WE_VERSION) . ' (' . WE_VERSION;
	$we_version .= ((defined('WE_SVNREV') && WE_SVNREV != '0000') ? ', SVN-Revision: ' . WE_SVNREV : '') . ')';
	$we_version .= (defined("WE_VERSION_SUPP") && WE_VERSION_SUPP != '') ? ' ' . g_l('global', '[' . WE_VERSION_SUPP . ']') : '';
	$we_version .= (defined("WE_VERSION_SUPP_VERSION") && WE_VERSION_SUPP_VERSION != '0') ? WE_VERSION_SUPP_VERSION : '';
}

if(isset($GLOBALS["loginpage"]) && WE_LOGIN_HIDEWESTATUS){
	$_logo = "info.jpg";
} elseif(defined("WE_VERSION_SUPP")){
	switch(strtolower(WE_VERSION_SUPP)){
		case "rc":
			$_logo = "info_rc.jpg";
			break;
		case "alpha":
			$_logo = "info_alpha.jpg";
			break;
		case "beta":
			$_logo = "info_beta.jpg";
			break;
		case "nightly":
		case "weekly":
		case "nightly-build":
			$_logo = "info_nightly.jpg";
			break;
		case "preview":
		case "dp":
			$_logo = "info_preview.jpg";
			break;
		case "trunk":
		case "svn":
			$_logo = "info_svn.jpg";
			break;
		default:
			$_logo = "info.jpg";
			break;
	}
}

$_table = new we_html_table(array(
		"style" => "border-style:none; padding:0px;border-spacing:0px;background-image:url(" . IMAGE_DIR . 'info/' . $_logo . ");background-repeat: no-repeat;background-color:#EBEBEB;width:" . $_widthTotal . 'px'),
		8,
		3);
$_actRow = 0;
//	First row with background
$_table->setCol($_actRow++, 0, array("colspan" => 3,
	"style" => 'width: ' . $_widthTotal . 'px;height:110px;',
	), '<a href="http://www.webedition.org" target="_blank"  title="www.webedition.org">' . we_html_tools::getPixel($_widthTotal, 110, 0) . '</a>'); //<br /><div class="defaultfont small" style="text-align:center;">Open Source Content Management</div>');

$_table->addRow(2);
//	spaceholder
$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
	"colspan" => 3), we_html_tools::getPixel($_widthTotal, 25));

//	3rd Version
if($we_version){
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	$_table->setCol($_actRow, 1, array("width" => $_middlePart,
		"class" => "small"), "Version: " . $we_version);

	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));
}

//	4th row with spaceholder
$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
	"colspan" => 3), we_html_tools::getPixel($_widthTotal, 5));


//	5th credits
$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
$_table->setCol($_actRow, 1, array("width" => $_middlePart,
	"class" => "defaultfont small"), $_credits);
$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

//	6th row
$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
	"colspan" => 3), we_html_tools::getPixel($_widthTotal, 10));

//	7th agency
if(is_readable($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'agency.php')){
	include_once($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'agency.php');
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
		"colspan" => 3), we_html_tools::getPixel($_widthTotal, 10));

	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart,
		"class" => "defaultfont small"), $_agency);
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));
}

//	8th row
$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
	"colspan" => 3), we_html_tools::getPixel($_widthTotal, 10));


if(isset($GLOBALS["loginpage"]) && $GLOBALS["loginpage"]){

	$loginRow = 0;

	$_loginTable = new we_html_table(
			array("style" => "border-style:none; padding:0px;border-spacing:0px;"
			),
			7,
			2
	);

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart, "class" => "small"), we_baseElement::getHtmlCode(new we_baseElement("label", true, array("for" => "username"), g_l('global', '[username]'))));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), we_html_tools::htmlTextInput("username", 25, "", 100, "id=\"username\" style=\"width: 250px;\" ", "text", 0, 0));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), we_html_tools::getPixel(5, 5));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart, "class" => "small"), we_baseElement::getHtmlCode(new we_baseElement("label", true, array("for" => "password"), g_l('global', '[password]'))));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), we_html_tools::htmlTextInput("password", 25, "", 32, "id=\"password\" style=\"width: 250px;\" ", "password", 0, 0));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart + $_logoPart, 'colspan' => 2), we_html_tools::getPixel(5, 5));


	$_table->addRow(4);
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array(), $_loginTable->getHtml());
	$_table->setCol($_actRow++, 2, array(), we_html_tools::getPixel($_space, 5));


	//	mode-table
	$_modetable = new we_html_table(array("border" => "0",
			"cellpadding" => "0",
			"cellspacing" => "0",
			"width" => $_middlePart),
			1,
			3);

	$loginButton = '<button type="submit" style="border: none; background-color: transparent; margin: 0px; padding: 0px;">' . we_button::create_button("login", "javascript:document.loginForm.submit();") . '</button>';
	if(!WE_SEEM){ //	deactivate See-Mode
		if(WE_LOGIN_WEWINDOW){
			$_modetable->setCol(0, 0, array(), '');
			if(WE_LOGIN_WEWINDOW == 1){
				$_modetable->setCol(0, 1, array("align" => "right",
					"valign" => "bottom",
					"rowspan" => "2"), '<input type="hidden" name="popup" value="popup"/>' . $loginButton);
			} else{
				$_modetable->setCol(0, 1, array("align" => "right",
					"valign" => "bottom",
					"rowspan" => "2"), $loginButton);
			}
		} else{
			$_modetable->setCol(0, 0, array(), we_forms::checkbox('popup', getValueLoginMode('popup'), 'popup', g_l('SEEM', '[popup]')));
			$_modetable->setCol(0, 1, array("align" => "right",
				"valign" => "bottom",
				"rowspan" => "2"), we_html_element::htmlHidden(array("name" => "mode", "value" => "normal")) . $loginButton);
		}
	} else{ //	normal login
		//	15th Mode
		$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
		$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small"), (!WE_SEEM ? '' : g_l('SEEM', '[start_mode]')));
		$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

		switch(WE_LOGIN_WEWINDOW){
			case 0:
				$we_login_type = we_forms::checkbox('popup', getValueLoginMode('popup'), 'popup', g_l('SEEM', '[popup]'));
				break;
			case 1:
				$we_login_type = '<input type="hidden" name="popup" value="popup"/>';
				break;
			default:
				$we_login_type = '';
		}

		// if button is between these radio boces, they can not be reachable with <tab>
		$_modetable->setCol(0, 0, array(), '<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>' . $we_login_type .
			'</td></tr><td>' .
			we_forms::radiobutton('normal', getValueLoginMode('normal'), 'mode', g_l('SEEM', "[start_mode_normal]"), true, "small") . '</td>
		</tr>
		<tr>
			<td>' . we_forms::radiobutton('seem', getValueLoginMode('seem'), 'mode', '<acronym title="' . g_l('SEEM', "[start_mode_seem_acronym]") . '">' . g_l('SEEM', "[start_mode_seem]") . '</acronym>', true, "small") . '</td>
		</tr>
		</table>');
		$_modetable->setCol(0, 1, array("align" => "right",
			"valign" => "bottom",
			"rowspan" => "3"), $loginButton);
	}

	//	16th
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small"), $_modetable->getHtml());
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	//	17th row
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 15));
} else if(isset($GLOBALS["loginpage"]) && !$GLOBALS["loginpage"]){

	srand((double) microtime() * 1000000);
	$r = rand();

	$loginRow = 0;

	$_content = g_l('global', '[loginok]');


	$_loginTable = new we_html_table(
			array("border" => 0,
				"cellpadding" => 0,
				"cellspacing" => 0
			),
			2,
			2
	);

	$_loginTable->setCol($loginRow, 0, array("width" => $_leftPart, "class" => "small"), $_content);
//	$_loginTable->setCol($loginRow++, 1, array('width' => $_logoPart, 'rowspan' => '5', 'height' => 60), '<img src="' . IMAGE_DIR . 'info/partnerLogo.gif" width="140" height="60" />');

	$_table->addRow(4);

	//	9th Login ok
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart,
		"class" => "small"), $_loginTable->getHtml());
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	//	10th row
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
		"colspan" => 3), we_html_tools::getPixel($_widthTotal, 5));
	//	11th back button
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart,
		"class" => "small",
		"align" => "right"), we_button::create_button("back_to_login", WEBEDITION_DIR . "index.php?r=$r"));
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	//	12th row
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal,
		"colspan" => 3), we_html_tools::getPixel($_widthTotal, 15));
} else if(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == "info"){
	$_table->addRow();
	$_table->setCol($_actRow++, 0, array("colspan" => "3"), we_html_tools::getPixel(2, 50));
}

if(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == "info"){
	print $_table->getHtml();
} else{
	$_loginTable = $_table->getHtml() . we_html_tools::getPixel(1, 1);
}
