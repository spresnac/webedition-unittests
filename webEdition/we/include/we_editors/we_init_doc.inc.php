<?php

/**
 * webEdition CMS
 *
 * $Rev: 5776 $
 * $Author: mokraemer $
 * $Date: 2013-02-09 17:23:51 +0100 (Sat, 09 Feb 2013) $
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
// exit if script called directly
if(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}
if(isset($GLOBALS['we_ContentType']) && !isset($we_ContentType)){
	$we_ContentType = $GLOBALS['we_ContentType'];
}
if((!isset($we_ContentType)) && ((!isset($we_dt)) || (!is_array($we_dt)) || (!$we_dt[0]['ClassName'])) && isset($we_ID) && $we_ID && isset($we_Table) && $we_Table){
	$we_ContentType = f('SELECT ContentType FROM ' . $GLOBALS['DB_WE']->escape($we_Table) . ' WHERE ID=' . intval($we_ID), 'ContentType', $GLOBALS['DB_WE']);
}
if(isset($we_ContentType) && $we_ContentType != ''){
	switch($we_ContentType){
		case 'application/x-shockwave-flash':
			$we_doc = new we_flashDocument();
			break;
		case 'video/quicktime':
			$we_doc = new we_quicktimeDocument();
			break;
		case 'image/*':
			$we_doc = new we_imageDocument();
			break;
		case 'folder':
			$we_doc = new we_folder();
			break;
		case 'class_folder':
			$we_doc = new we_class_folder();
			break;
		case 'text/weTmpl':
			$we_doc = new we_template();
			break;
		case 'text/webedition':
			$we_doc = new we_webEditionDocument();
			break;
		case 'text/html':
			$we_doc = new we_htmlDocument();
			break;
		case 'text/xml':
		case 'text/js':
		case 'text/css':
		case 'text/plain':
		case 'text/htaccess':
			$we_doc = new we_textDocument();
			break;
		case 'application/*':
			$we_doc = new we_otherDocument();
			break;
		default:
			$moduleDir = we_getModuleNameByContentType($we_ContentType);
			if($moduleDir != ''){
				$moduleDir .= '/';
			}

			if(file_exists(WE_MODULES_PATH . $moduleDir . 'we_' . $we_ContentType . '.inc.php')){
				$we_doc = 'we_' . $we_ContentType;
				$we_doc = new $we_doc();
			} else{
				t_e('Can NOT initialize document of type -' . $we_ContentType . '- ' . WE_MODULES_PATH . $moduleDir . 'we_' . $we_ContentType . '.inc.php');
				exit(1);
			}
	}
} else{
	if(isset($we_dt[0]['ClassName']) && $we_dt[0]['ClassName']){
		$we_doc = $we_dt[0]['ClassName'];
		$we_doc = new $we_doc();
	} else{
		$we_doc = new we_webEditionDocument();
	}
}
if(isset($we_ID)){
	$we_doc->initByID($we_ID, $we_Table, ( (isset($GLOBALS['FROM_WE_SHOW_DOC']) && $GLOBALS['FROM_WE_SHOW_DOC']) || (isset($GLOBALS['WE_RESAVE']) && $GLOBALS['WE_RESAVE']) ) ? we_class::LOAD_MAID_DB : we_class::LOAD_TEMP_DB);
} else if(isset($we_dt)){
	$we_doc->we_initSessDat($we_dt);
//	in some templates we must disable some EDIT_PAGES and disable some buttons
	$we_doc->executeDocumentControlElements();
} else{
	$we_doc->ContentType = $we_ContentType;
	$we_doc->Table = (isset($we_Table) && $we_Table) ? $we_Table : FILE_TABLE;
	$we_doc->we_new();
}

//FIXME: remove this clone
$GLOBALS['we_doc'] = clone($we_doc);

//if document opens get initial object for versioning if no versions exist
if(isset($_REQUEST['we_cmd'][0]) && ($_REQUEST['we_cmd'][0] == 'load_edit_footer' || $_REQUEST['we_cmd'][0] == 'switch_edit_page')){
	$version = new weVersions();
	$version->setInitialDocObject($GLOBALS['we_doc']);
}