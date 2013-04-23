<?php

/**
 * webEdition CMS
 *
 * $Rev: 5546 $
 * $Author: mokraemer $
 * $Date: 2013-01-08 23:00:54 +0100 (Tue, 08 Jan 2013) $
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
/* * ***************************************************************************
 * INCLUDES
 * *************************************************************************** */

include(WE_INCLUDES_PATH . 'we_editors/we_preferences_header.inc.php');
include_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

we_html_tools::protect();
we_html_tools::htmlTop();
print STYLESHEET . getPreferencesCSS();

$tabname = isset($_REQUEST["tabname"]) ? $_REQUEST["tabname"] : (isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : "setting_ui");

// Define needed JS
$_javascript = <<< END_OF_SCRIPT

function we_cmd() {
	//var url = "/webEdition/we/include/we_editors/we_preferences.php?";

	switch (arguments[0]) {
END_OF_SCRIPT;
foreach($GLOBALS['tabs'] as $name => $perm){
	if(empty($perm) || we_hasPerm($perm)){
		$_javascript.='case "' . $name . '":' . "\n";
	}
}
foreach($GLOBALS['tabs'] as $name => $perm){
	$_javascript.="content.document.getElementById('setting_" . $name . "').style.display = 'none';";
}

$_javascript .= "
			content.document.getElementById('setting_' + arguments[0]).style.display = '';
			break;
	}
}
self.focus();

function closeOnEscape() {
	return true;

}

function saveOnKeyBoard() {
	window.frames[2].we_save();
	return true;

}";


print we_html_element::jsElement($_javascript) .
	we_html_element::jsScript(JS_DIR . "keyListener.js") . "</head>";

include(WE_INCLUDES_PATH . 'we_editors/we_preferences_footer.inc.php');

$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', 'onload' => 'setFrameSize()', 'onresize' => 'setFrameSize()')
		, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
			, we_html_element::htmlExIFrame('navi', getPreferencesHeader(), 'position:absolute;top:0px;height:' . getPreferencesTabsDefaultHeight() . 'px;left:0px;right:0px;overflow: hidden;') .
			we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . "we_editors/we_preferences.php?setting=ui" . ($tabname != "" ? "&tabname=" . $tabname : ""), 'position:absolute;top:' . getPreferencesTabsDefaultHeight() . 'px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'border:0px;width:100%;height:100%;overflow: scroll;') .
			we_html_element::htmlExIFrame('we_preferences_footer', getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
		));

print we_html_element::htmlBody(array(), $body) . getPreferencesJS() . getPreferencesFooterJS() . '</html>';