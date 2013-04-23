<?php

/**
 * webEdition CMS
 *
 * $Rev: 4410 $
 * $Author: mokraemer $
 * $Date: 2012-04-17 13:10:31 +0200 (Tue, 17 Apr 2012) $
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
function we_tag_ifWorkspace($attribs){
	$required_path = weTag_getAttribute('path', $attribs);
	$docAttr = weTag_getAttribute("doc", $attribs, "self");
	$doc = we_getDocForTag($docAttr);
	$id = explode(',', weTag_getAttribute('id', $attribs));

	if($required_path){
		$required_path = array(substr($required_path, 0, 1) != '/' ? '/' . $required_path : $required_path);
	}

	if(!$required_path){
		$required_path = id_to_path($id, FILE_TABLE, $GLOBALS['DB_WE'], false, true);
	}

	if(!$required_path){
		return false;
	}

	foreach($required_path as $path){
		if(strpos($doc->Path, $path) === 0){
			return true;
		}
	}

	return false;
}
