<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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

$_callback = isset($_REQUEST['callback'])?$_REQUEST['callback']:'';

$js = we_html_element::jsElement('

		var wait_count = 0;
		var wait_retry = 40;

		function nojava() {
				alert("' . g_l('eplugin', '[no_java]') . '");
				top.opener.top.plugin.location="' . WEBEDITION_DIR . 'html/white.html";
				self.close();
		}

		function checkPlugin() {
			if(top.opener.top.plugin.isLoaded && typeof(top.opener.top.plugin.document.WePlugin)!="undefined") {
				if(typeof(top.opener.top.plugin.document.WePlugin.isLive)!="undefined") {
					' . (!empty($_callback) ? ('eval("top.opener.' . $_callback . '");') : '') . '
					self.close();
				} else {
					nojava();
				}
			} else {
				wait_count ++;
				if(wait_count<wait_retry) {
					setTimeout("checkPlugin()",1000);
				} else {
					nojava();
				}
			}

		}

		function initPlugin() {
			top.opener.top.plugin.location="' . WEBEDITION_DIR . 'editors/content/eplugin/weplugin.php";
		}

		self.focus();
	');
$css = we_html_element::cssLink(WEBEDITION_DIR . 'css/global.php');

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead($css . "\n" . $js) .
		we_html_element::htmlBody(array("bgcolor" => "#ffffff", "leftmargin" => "20", "topmargin" => "20", "marginheight" => "20", "marginwidth" => "20", "onload" => "initPlugin();checkPlugin()"), we_html_element::htmlForm(array("name" => "we_form"), we_html_element::htmlCenter(
					we_html_element::htmlImg(array("src" => IMAGE_DIR . "spinner.gif")) .
					we_html_element::htmlBr() .
					we_html_element::htmlBr() .
					we_html_element::htmlDiv(array("class" => "header_small"), g_l('eplugin', "[initialisation]"))
				)
			)
		)
	);