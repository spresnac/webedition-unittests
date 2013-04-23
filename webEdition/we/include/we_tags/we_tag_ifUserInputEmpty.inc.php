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
function we_isUserInputNotEmpty($attribs){
	$formname = weTag_getAttribute('formname', $attribs, 'we_global_form');
	$match = weTag_getAttribute('match', $attribs);
	return (isset($_REQUEST['we_ui_' . $formname][$match]) && strlen($_REQUEST['we_ui_' . $formname][$match]));
}

function we_tag_ifUserInputEmpty($attribs){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		print($foo);
		return '';
	}
	return !we_isUserInputNotEmpty($attribs);
}
