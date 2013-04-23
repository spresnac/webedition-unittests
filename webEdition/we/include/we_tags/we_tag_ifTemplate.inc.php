<?php

/**
 * webEdition CMS
 *
 * $Rev: 5576 $
 * $Author: mokraemer $
 * $Date: 2013-01-16 21:56:32 +0100 (Wed, 16 Jan 2013) $
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
function we_tag_ifTemplate($attribs){
	$id = weTag_getAttribute('id', $attribs);
	$workspaceID = weTag_getAttribute('workspaceID', $attribs);
	$path = weTag_getAttribute('path', $attribs);
	$TID = (isset($GLOBALS['we_doc']->TemplateID) ? $GLOBALS['we_doc']->TemplateID : ($GLOBALS['we_doc'] instanceof we_template && isset($GLOBALS['we_doc']->ID) ? $GLOBALS['we_doc']->ID : 0));

	if($TID && $id !== ''){
		return in_array($TID, makeArrayFromCSV($id));
	} else{
		if($workspaceID !== ''){
			if(isset($GLOBALS['we_doc']->TemplatePath)){ // in documents
				$curTempPath = str_replace(TEMPLATES_PATH, '', $GLOBALS['we_doc']->TemplatePath);
			} else{ // in templates
				$curTempPath = $GLOBALS['we_doc']->Path;
			}
			$path = f('SELECT DISTINCT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($workspaceID) . ' LIMIT 1', 'Path', $GLOBALS['DB_WE']);
			return (($path != '') && strpos($curTempPath, $path) !== false && strpos($curTempPath, $path) == 0);
		} else{
			if($path === ''){
				return true;
			}
			if(isset($GLOBALS['we_doc']->TemplatePath)){
				$pathReg = "|^" . str_replace("\\*", '.*', preg_quote($path, '|')) . "\$|";
				return preg_match($pathReg, $GLOBALS['we_doc']->TemplatePath);
			}
		}
	}
	return false;
}
