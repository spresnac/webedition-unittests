<?php

/**
 * webEdition CMS
 *
 * $Rev: 5060 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 16:57:00 +0100 (Sun, 04 Nov 2012) $
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
function we_tag_ifEditmode($attribs){
	$doc = weTag_getAttribute('doc', $attribs);
	switch($doc){
		case 'self' :
			return $GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc'] && isset($GLOBALS['we_editmode']) && $GLOBALS["we_editmode"];
		default :
			return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) || $GLOBALS['WE_MAIN_EDITMODE']/* || (isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'seem') */;
	}
}
