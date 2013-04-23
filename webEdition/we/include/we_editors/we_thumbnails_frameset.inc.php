<?php

/**
 * webEdition CMS
 *
 * $Rev: 3953 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 19:12:45 +0100 (Tue, 07 Feb 2012) $
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

we_html_tools::htmlTop(g_l('thumbnails', '[thumbnails]'));

//  check if gd_lib is installed ...
if(we_image_edit::gd_version() > 0){

	print
		we_html_element::jsElement('self.focus();') .
		we_html_element::jsScript(JS_DIR . "keyListener.js") .
		we_html_element::jsElement(
			"
    			function closeOnEscape() {
					return true;

				}

				function saveOnKeyBoard() {
					window.frames[1].we_save();
					return true;

				}"
		) .
		"</head>";

	$frameset = new we_html_frameset(array("rows" => "*,40", "framespacing" => "0", "border" => "1", "frameborder" => "no"), 0);
	$frameset->addFrame(array("src" => WEBEDITION_DIR . "we/include/we_editors/we_thumbnails.php", "name" => "we_thumbnails", "scrolling" => "auto", "noresize" => "noresize"));
	$frameset->addFrame(array("src" => WEBEDITION_DIR . "we/include/we_editors/we_thumbnails_footer.php?closecmd=" . (isset($_REQUEST['we_cmd'][1]) ? rawurlencode($_REQUEST['we_cmd'][1]) : ""), "name" => "we_thumbnails_footer", "scrolling" => "no", "noresize" => "noresize"));

	print $frameset->getHtml() . we_html_element::htmlBody(array()) . "</html>";
} else{ //  gd_lib is not installed - show error
	print STYLESHEET . '</head><body class="weDialogBody">';


	$parts = array();
	array_push($parts, array("headline" => "",
		"html" => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', "[add_description_nogdlib]"), 2, 440),
		"space" => 0
		)
	);
	$content = we_multiIconBox::getHTML("weimportfiles", "100%", $parts, 30, "", -1, '', '', false, g_l('thumbnails', '[thumbnails]'));

	print $content;
}

