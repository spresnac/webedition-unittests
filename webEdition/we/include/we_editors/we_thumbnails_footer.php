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

/* * ***************************************************************************
 * CREATE JAVASCRIPT
 * *************************************************************************** */

// Define needed JS
$_javascript = <<< END_OF_SCRIPT
<!--
function we_save() {
	top.we_thumbnails.document.getElementById('thumbnails_dialog').style.display = 'none';

	top.we_thumbnails.document.getElementById('thumbnails_save').style.display = '';

	top.we_thumbnails.document.we_form.save_thumbnails.value = 'true';

	top.we_thumbnails.document.we_form.submit();
}


//-->
END_OF_SCRIPT;

/* * ***************************************************************************
 * RENDER FILE
 * *************************************************************************** */

print STYLESHEET . we_html_element::jsElement($_javascript) . "</head>";


$okbut = we_button::create_button("save", "javascript:we_save();");
$cancelbut = we_button::create_button("close", "javascript:" . ((isset($_REQUEST["closecmd"]) && $_REQUEST["closecmd"]) ? ($_REQUEST["closecmd"] . ";") : "") . "top.close()");

print we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), we_button::position_yes_no_cancel($okbut, "", $cancelbut, 10, "", "", 0) . "</html>");

