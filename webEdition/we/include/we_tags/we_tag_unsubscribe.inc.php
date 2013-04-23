<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_tag_unsubscribe($attribs){

	$attribs['type'] = 'text';
	$attribs['name'] = 'we_unsubscribe_email__';

	$attribs['value'] = (isset($_REQUEST["we_unsubscribe_email__"]) ? filterXss($_REQUEST["we_unsubscribe_email__"]) : '');

	return getHtmlTag('input', $attribs);
}
