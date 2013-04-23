<?php

/**
 * webEdition CMS
 *
 * $Rev: 4550 $
 * $Author: mokraemer $
 * $Date: 2012-05-18 18:05:32 +0200 (Fri, 18 May 2012) $
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

print we_html_tools::htmlTop() .
	we_html_element::jsElement(
		"function init() {
	parent.executeAjaxRequest('" . implode("', '", $_REQUEST['we_cmd']) . "');

}") .
	we_html_element::htmlBody(array('onload' => 'init()')) .
	'</html>';
