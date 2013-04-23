<?php

/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');

we_html_tools::protect();
$jsCode = "
function init(){
	_fo=document.forms[0];
	initPrefs();
}

function save(){
	savePrefs();
	previewPrefs();
	" . we_message_reporting::getShowMessageCall(
		g_l('cockpit', '[prefs_saved_successfully]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
	self.close();
}

function preview(){ previewPrefs(); }

function exit_close(){
	previewPrefs();
	exitPrefs();
	self.close();
}
";

$parts = array(
	array(
		"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_button::create_button("save", "javascript:save();", false, -1, -1);
$preview_button = we_button::create_button("preview", "javascript:preview();", false, -1, -1);
$cancel_button = we_button::create_button("close", "javascript:exit_close();");
$buttons = we_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_multiIconBox::getHTML("rssProps", "100%", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[messaging]'));

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[messaging]')) . STYLESHEET . we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
			we_html_element::jsElement(
				$jsPrefs . $jsCode . we_button::create_state_changer(false))) . we_html_element::htmlBody(
			array(
			"class" => "weDialogBody", "onload" => "init();"
			), we_html_element::htmlForm("", $sTblWidget)));