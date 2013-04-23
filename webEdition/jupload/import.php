<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
if(empty($_SESSION["user"]["Username"]) && isset($_REQUEST['csid'])){
	session_id($_REQUEST['csid']);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

$import_files = new we_import_files();

if(isset($_SESSION['weS']['_we_import_files'])){
	$import_files->loadPropsFromSession();
}

if(isset($_REQUEST['pathinfo0'])){
	$_SESSION["prefs"]['juploadPath'] = $_REQUEST['pathinfo0'];
}

$_counter = 0;
foreach($_FILES as $_index => $_file){
	if(strpos($_index, 'File') === 0 && $_file['error'] == 0){
		$_FILES['we_File'] = $_file;

		$error = $import_files->importFile();

		if(!empty($error)){
			if(!isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){
				$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] = array();
			}
			$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'][] = $error;
		}

		flush();
		unset($_FILES['we_File']);
		$_counter++;
	} else{
		break;
	}
}

if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){
	echo 'ERROR: ';
	foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
		echo '- ' . $err['filename'] . ' => ' . $err['error'] . '\n';
	}
	echo "\n";
	t_e('import error', $_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);
	unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);
} else{
	echo "SUCCESS\n";
}

flush();