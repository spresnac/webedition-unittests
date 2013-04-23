<?php

/**
 * webEdition CMS
 *
 * $Rev: 5896 $
 * $Author: mokraemer $
 * $Date: 2013-02-26 14:53:36 +0100 (Tue, 26 Feb 2013) $
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
if(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

$DB_WE->query('DELETE FROM ' . LOCK_TABLE . ' WHERE UserID=' . intval($_SESSION["user"]["ID"]) . ' AND sessionID="' . session_id() . '"');
//FIXME: table is set to false value, if 2 sessions are open; but this is updated shortly - so ignore it now
//TODO: update to time if still locked files open
$DB_WE->query('UPDATE ' . USER_TABLE . ' SET Ping=0 WHERE ID=' . intval($_SESSION["user"]["ID"]));

cleanTempFiles(true);

//FIXME: is there any need for this?
if(isset($_SESSION["prefs"]["userID"])){ //	bugfix 2585, only update prefs, when userId is available
	we_user::writePrefs($_SESSION["prefs"]["userID"], $GLOBALS['DB_WE']);
}

//	getJSCommand
if(isset($_SESSION['weS']['SEEM']["startId"])){ // logout from webEdition opened with tag:linkToSuperEasyEditMode
	$keys = array_keys($_SESSION);
	foreach($keys as $key){
		if($key != "webuser"){
			unset($_SESSION[$key]);
		}
	}
	$_path = $_SESSION['weS']['SEEM']["startPath"];
} else{ //	normal logout from webEdition.
	unset($_SESSION["user"]);
	if(isset($_SESSION['weS'])){
		unset($_SESSION['weS']);
	}
	$_path = WEBEDITION_DIR;
}

if(isset($_SESSION)){
	unset($_SESSION['weS']);
}

//FIXME: this should be removed if all variables are located inside weS; fix other!!
if(isset($_SESSION)){
	unset($_SESSION['weS']);
	while((list($name, $val) = each($_SESSION))) {
		unset($_SESSION[$name]);
	}
}
$_SESSION = array();

if(!isset($GLOBALS['isIncluded']) || !$GLOBALS['isIncluded']){
	echo we_html_element::jsElement('
	for(i=0;i<top.jsWindow_count;i++){
		eval("var obj=top.jsWindow"+i+"Object");
		try{
			obj.close();
		}catch(err){}
	}

	if(typeof(top.tinyMceDialog) !== "undefinded" && top.tinyMceDialog !== null){
		var tinyDialog = top.tinyMceDialog;
		try{
			tinyDialog.close();
		}catch(err){}
	}

	if(typeof(top.tinyMceSecondaryDialog) !== "undefinded" && top.tinyMceSecondaryDialog !== null){
		var tinyDialog = top.tinyMceSecondaryDialog;
		try{
			tinyDialog.close();
		}catch(err){}
	}

	if(top.opener){ // we was opened in popup
		top.opener.location.replace("' . $_path . '");
		top.close();
		top.opener.focus();
	} else{
		top.location.replace("' . $_path . '");
	}
');
}
