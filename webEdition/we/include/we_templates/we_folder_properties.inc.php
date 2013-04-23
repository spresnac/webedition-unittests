<?php

/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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
$parts = array();

array_push($parts, array("icon" => "path.gif", "headline" => g_l('weClass', "[path]"), "html" => $GLOBALS['we_doc']->formPath(), "space" => 140));


if($we_doc->Table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $we_doc->Table == OBJECT_FILES_TABLE)){
	if(isset($_SESSION["perms"]["ADMINISTRATOR"]) && $_SESSION["perms"]["ADMINISTRATOR"]){
		array_push($parts, array("icon" => "lang.gif", "headline" => g_l('weClass', "[language]"), "html" => $GLOBALS['we_doc']->formLanguage(), "space" => 140, "noline" => 1));
		array_push($parts, array("headline" => g_l('weClass', "[grant_language]"), "html" => $GLOBALS['we_doc']->formChangeLanguage(), "space" => 140, "forceRightHeadline" => 1));
	} else if($we_doc->Table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $we_doc->Table == OBJECT_FILES_TABLE)){
		array_push($parts, array("icon" => "lang.gif", "headline" => g_l('weClass', "[language]"), "html" => $GLOBALS['we_doc']->formLanguage(), "space" => 140));
	}
}

if($we_doc->Table == FILE_TABLE && we_hasPerm("CAN_COPY_FOLDERS") || (defined('OBJECT_FILES_TABLE') && $we_doc->Table == OBJECT_FILES_TABLE && we_hasPerm("CAN_COPY_OBJECTS"))){
	array_push($parts, array("icon" => "copy.gif", "headline" => g_l('weClass', "[copyFolder]"), "html" => $GLOBALS['we_doc']->formCopyDocument(), "space" => 140));
}

$wepos = weGetCookieVariable("but_weDirProp");
$znr = 4;
if($we_doc->Table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $we_doc->Table == OBJECT_FILES_TABLE)){
	array_push($parts, array("icon" => "user.gif", "headline" => g_l('weClass', "[owners]")
		, "html" => $GLOBALS['we_doc']->formCreatorOwners() . "<br>", "space" => 140, "noline" => 1));
	if(isset($_SESSION["perms"]["ADMINISTRATOR"]) && $_SESSION["perms"]["ADMINISTRATOR"]){
		array_push($parts, array("headline" => g_l('modules_users', "[grant_owners]"), "html" => $GLOBALS['we_doc']->formChangeOwners(), "space" => 140, "forceRightHeadline" => 1));
	}
}

if(count($parts) == 1){
	$znr = -1;
}

print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("weDirProp", "100%", $parts, 20);
