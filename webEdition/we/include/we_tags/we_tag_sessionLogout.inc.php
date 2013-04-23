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
function we_tag_sessionLogout($attribs, $content){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}
	$id = weTag_getAttribute('id', $attribs);
	$id = ($id == 'self') ? $GLOBALS['WE_MAIN_DOC']->ID : $id;
	$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $GLOBALS['DB_WE']);

	$url = (!empty($row) ? $row['Path'] . ($row['IsFolder'] ? '/' : '') : '');
	$attr = we_make_attribs($attribs, 'id,_name_orig');

	return '<a href="' . $url . '?we_webUser_logout=1" ' . ($attr ? $attr : '') . '>' . $content . '</a>';
}