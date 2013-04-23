<?php
/**
 * webEdition CMS
 *
 * $Rev: 4391 $
 * $Author: mokraemer $
 * $Date: 2012-04-04 20:25:15 +0200 (Wed, 04 Apr 2012) $
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

$tabname = isset($_REQUEST["tabname"]) && $_REQUEST["tabname"] != "" ? $_REQUEST["tabname"] : "setting_ui";
/* * ***************************************************************************
 * GENERATE JAVASCRIPT
 * *************************************************************************** */


// generate the tabs

$we_tabs = new we_tabs();

$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_ui]'), ($tabname == "setting_ui" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('ui');"));


if(we_hasPerm("EDIT_SETTINGS_DEF_EXT")){
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_extensions]'), ($tabname == "setting_extensions" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('extensions');"));
}


$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_editor]'), ($tabname == "setting_editor" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('editor');"));


if(we_hasPerm("ADMINISTRATOR")){

	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_proxy]'), ($tabname == "setting_proxy" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('proxy');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_advanced]'), ($tabname == "setting_advanced" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('advanced');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_system]'), ($tabname == "setting_system" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('system');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_seolinks]'), ($tabname == "setting_seolinks" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('seolinks');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[module_activation][headline]'), ($tabname == "setting_active_integrated_modules" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('active_integrated_modules');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_language]'), ($tabname == "setting_language" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('language');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_countries]'), ($tabname == "setting_countries" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('countries');"));

	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_error_handling]'), ($tabname == "tab_error_handling" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('error_handling');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[backup]'), ($tabname == "setting_backup" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('backup');"));
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[validation]'), ($tabname == "setting_validation" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('validation');"));


	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_email]'), ($tabname == "setting_email" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('email');"));
}
// add message_reporting tab
$we_tabs->addTab(new we_tab("#", g_l('prefs', '[message_reporting][headline]'), ($tabname == "setting_message_reporting" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('message_reporting');"));

if(we_hasPerm("FORMMAIL")){
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_formmail]'), ($tabname == "setting_recipients" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('recipients');"));
}
if(we_hasPerm("ADMINISTRATOR")){
	$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab_versions]'), ($tabname == "setting_versions" ? 'TAB_ACTIVE' : 'TAB_NORMAL'), "top.we_cmd('versions');"));
}
$we_tabs->onResize('we_preferences_header');
$tab_head = $we_tabs->getHeader('', 1);
$tab_js = $we_tabs->getJS();

/* * ***************************************************************************
 * RENDER FILE
 * *************************************************************************** */
$bodyContent = '<div id="main" >' . $we_tabs->getHTML() . '</div>';

print $tab_head . '</head>';
print we_html_element::htmlBody(array("bgcolor" => "#ffffff", "background" => IMAGE_DIR . "backgrounds/header.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"), $bodyContent);
?></html>