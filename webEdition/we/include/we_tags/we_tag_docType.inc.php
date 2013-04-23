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
function we_tag_docType($attribs){
	$docAttr = weTag_getAttribute("doc", $attribs);
	switch($docAttr){
		case "self" :
			if($GLOBALS['we_doc']->DocType){
				return f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID = ' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->DocType), "DocType", $GLOBALS['DB_WE']);
			}
			break;
		case "top" :
		default :
			if(isset($GLOBALS["WE_MAIN_DOC"])){
				if($GLOBALS["WE_MAIN_DOC"]->DocType){
					return f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID = ' . $GLOBALS['DB_WE']->escape($GLOBALS["WE_MAIN_DOC"]->DocType), 'DocType', $GLOBALS['DB_WE']);
				}
			} elseif($GLOBALS['we_doc']->DocType){ // if we_doc is the "top-document"
				return f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID = ' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->DocType), 'DocType', $GLOBALS['DB_WE']);
			}
			break;
	}
	return '';
}
