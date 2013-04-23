<?php

/**
 * webEdition CMS
 *
 * $Rev: 4404 $
 * $Author: mokraemer $
 * $Date: 2012-04-13 23:15:29 +0200 (Fri, 13 Apr 2012) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* this file contains vertical tabs
 * it has variables:
 * - $allTabs => array of all tabnames
 * - $activeTab => current selected tab
 */

// initialise tabs
$tabs = new we_tabs();
foreach($this->Data['allTabs'] as $tabname){
	$tabs->addTab(new we_tab("#", g_l('liveUpdate', '[tabs][' . $tabname . ']'), ($this->Data['activeTab'] == $tabname ? "TAB_ACTIVE" : "TAB_NORMAL"), "top.frames.updatecontent.location='?section=$tabname';"));
}


// get output
$tabs->onResize();
$_tabHead = $tabs->getHeader();
$_tabJs = $tabs->getJS();

$bodyContent = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;" id="headrow">' . we_html_tools::getPixel(100, 10) . '</div>' . we_html_tools::getPixel(100, 3) .
	$tabs->getHTML() .
	'</div>';

$_body = we_html_element::htmlBody(array("bgcolor" => "#ffffff",
		"topmargin" => "0",
		"background" => IMAGE_DIR . "backgrounds/header.gif",
		"onload" => "setFrameSize();",
		"onresize" => "setFrameSize()"), $bodyContent);

print we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead($_tabHead) . "\n" . $_body);
