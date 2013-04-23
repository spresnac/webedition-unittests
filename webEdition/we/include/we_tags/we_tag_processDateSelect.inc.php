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
function we_tag_processDateSelect($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute("name", $attribs);
	$endofday = weTag_getAttribute("endofday", $attribs, false, true);
	$GLOBALS[$name] = $_REQUEST[$name] = mktime(
		$endofday ? 23 : 0, $endofday ? 59 : 0, $endofday ? 59 : 0, isset($_REQUEST[$name . "_month"]) ? intval($_REQUEST[$name . "_month"]) : 0, isset($_REQUEST[$name . "_day"]) ? intval($_REQUEST[$name . "_day"]) : 0, isset($_REQUEST[$name . "_year"]) ? intval($_REQUEST[$name . "_year"]) : 0);
}
