<?php

/**
 * webEdition CMS
 *
 * $Rev: 5600 $
 * $Author: mokraemer $
 * $Date: 2013-01-20 10:48:53 +0100 (Sun, 20 Jan 2013) $
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
we_html_tools::protect();

/**
 * Checks if the start-document is a valid document. Content Type text/webedition or text/html
 * @return bool
 * @param int $id
 */
function checkIfValidStartdocument($id, $type = 'document'){

	if($type == 'object'){
		return (f('SELECT ContentType FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), 'ContentType', $GLOBALS['DB_WE']) == 'objectFile');
	} else{
		return (f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'ContentType', $GLOBALS['DB_WE']) == 'text/webedition');
	}
}

//	Here begins the code for showing the correct frameset.
//	To improve readability the different cases are outsourced
//	in several functions, for SEEM, normal or edit_include-Mode.


function _buildJsCommand($cmdArray = array('', '', 'cockpit', 'open_cockpit', '', '', '', '', '')){
	return 'if(top && top.weEditorFrameController) top.weEditorFrameController.openDocument("' . implode('", "', $cmdArray) . '");';
}

$jsCommand = _buildJsCommand();
if(isset($_REQUEST['we_cmd']) && isset($_REQUEST['we_cmd'][4]) && $_REQUEST['we_cmd'][4] == 'SEEM_edit_include'){ // Edit-Include-Mode
// in multiEditorFrameset we_cmd[1] can be set to reach this
	$directCmd = array();
	for($i = 1; $i < count($_REQUEST['we_cmd']) && $i < 4; $i++){
		$directCmd[] = $_REQUEST['we_cmd'][$i];
	}
	$jsCommand = _buildJsCommand($directCmd);
} else{ // check preferences for which document to open at startup
// <we:linkToSeeMode> !!!!
	if(isset($_SESSION['weS']['SEEM']) && isset($_SESSION['weS']['SEEM']['open_selected'])){
		switch($_SESSION['weS']['SEEM']['startType']){
			case 'document':
				if(checkIfValidStartdocument($_SESSION['weS']['SEEM']['startId'])){
					$directCmd = array(
						FILE_TABLE,
						$_SESSION['weS']['SEEM']['startId'],
						'text/webedition',
					);
					$jsCommand = _buildJsCommand($directCmd);
				} else{
					t_e('invalid start doc ' . $_SESSION['weS']['SEEM']['startId']);
				}
				break;
			case 'object':
				if(checkIfValidStartdocument($_SESSION['weS']['SEEM']['startId'])){
					$directCmd = array(
						OBJECT_FILES_TABLE,
						$_SESSION['weS']['SEEM']['startId'],
						'objectFile'
					);
					$jsCommand = _buildJsCommand($directCmd);
				} else{
					t_e('invalid start doc ' . $_SESSION['weS']['SEEM']['startId']);
				}
				break;
		}
		unset($_SESSION['weS']['SEEM']['open_selected']);

// normal mode, start document depends on settings
	} else{
		switch($_SESSION['prefs']['seem_start_type']){
			case 'object':
				if($_SESSION['prefs']['seem_start_file'] != 0 && checkIfValidStartdocument($_SESSION['prefs']['seem_start_file'], 'object')){ //	if a stardocument is already selected - show this
					$directCmd = array(
						OBJECT_FILES_TABLE,
						$_SESSION['prefs']['seem_start_file'],
						'objectFile',
					);
					$jsCommand = _buildJsCommand($directCmd);
				} else{
					t_e('start doc not valid');
				}
				break;
			case '0':
				$_SESSION['prefs']['seem_start_type'] = '0';
				break;
			case 'document':
				if($_SESSION['prefs']['seem_start_file'] != 0 && checkIfValidStartdocument($_SESSION['prefs']['seem_start_file'])){ //	if a stardocument is already selected - show this
					$directCmd = array(
						FILE_TABLE,
						$_SESSION['prefs']['seem_start_file'],
						'text/webedition',
					);
					$jsCommand = _buildJsCommand($directCmd);
				} else{
					if($_SESSION['prefs']['seem_start_file'] != 0){
						t_e('start doc not valid', $_SESSION['prefs']['seem_start_file']);
					}
				}
				break;
			case 'weapp':
				if($_SESSION['prefs']['seem_start_weapp'] != ''){ //	if a we-app is choosen
					$directCmd = array(
						'', '', '', 'tool_' . $_SESSION['prefs']['seem_start_weapp'] . '_edit'
					);
					$jsCommand = _buildJsCommand() .
						_buildJsCommand($directCmd);
				}
				break;
		}
	}
}
if($_SESSION['prefs']['seem_start_type'] !== '0'){
	print we_html_element::jsElement($jsCommand);
} else{
	print we_html_element::jsElement('top.weEditorFrameController.toggleFrames();');
}
