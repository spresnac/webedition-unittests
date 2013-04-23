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
$wepos = "";
$parts = array();

if($GLOBALS['we_doc']->EditPageNr != WE_EDITPAGE_WORKSPACE){
	array_push($parts, array(
		"headline" => g_l('weClass', "[path]"),
		"html" => $GLOBALS['we_doc']->formPath(),
		"space" => 140,
		"icon" => "path.gif")
	);

	if($_SESSION['weS']['we_mode'] == "seem" || !we_hasPerm('CAN_SEE_OBJECTS')){ // No link to class in normal mode
		array_push($parts, array(
			"headline" => g_l('modules_object', '[class]'),
			"html" => $GLOBALS['we_doc']->formClass(),
			"space" => 140,
			'noline' => true,
			"icon" => "class.gif")
		);
	} else if($_SESSION['weS']['we_mode'] == "normal"){ //	Link to class in normal mode
		$_html = '<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;"><a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_TABLE . '\',' . $GLOBALS['we_doc']->TableID . ',\'object\');">' . g_l('modules_object', '[class]') . '</a></div>' .
			'<div style="margin-bottom:12px;">' . $GLOBALS['we_doc']->formClass() . '</div>';
		$_html .= '<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">' . g_l('modules_object', '[class_id]') . '</div>' .
			'<div style="margin-bottom:12px;">' . $GLOBALS['we_doc']->formClassId() . '</div>';


		array_push($parts, array(
			"headline" => "",
			"html" => $_html,
			"space" => 140,
			"forceRightHeadline" => 1,
			"icon" => "class.gif")
		);
	}

	array_push($parts, array(
		"headline" => g_l('weClass', "[language]"),
		"html" => $GLOBALS['we_doc']->formLanguage(),
		"space" => 140,
		"icon" => "lang.gif")
	);


	array_push($parts, array(
		"headline" => g_l('global', "[categorys]"),
		"html" => $GLOBALS['we_doc']->formCategory(),
		"space" => 140,
		"icon" => "cat.gif")
	);


	array_push($parts, array(
		"headline" => g_l('modules_object', '[copyObject]'),
		"html" => $GLOBALS['we_doc']->formCopyDocument(),
		"space" => 140,
		"icon" => "copy.gif")
	);


	array_push($parts, array(
		"headline" => g_l('weClass', "[owners]"),
		"html" => $GLOBALS['we_doc']->formCreatorOwners(),
		"space" => 140,
		"icon" => "user.gif")
	);


	array_push($parts, array(
		"headline" => g_l('weClass', "[Charset]"),
		"html" => $GLOBALS['we_doc']->formCharset(),
		"space" => 140,
		"icon" => "charset.gif")
	);
} else{

	if($GLOBALS['we_doc']->hasWorkspaces()){ //	Show workspaces
		array_push($parts, array(
			"headline" => g_l('weClass', "[workspaces]"),
			"html" => $GLOBALS['we_doc']->formWorkspaces(),
			"space" => 140,
			"noline" => 1,
			"icon" => "workspace.gif")
		);
		array_push($parts, array(
			"headline" => g_l('weClass', "[extraWorkspaces]"),
			"html" => $GLOBALS['we_doc']->formExtraWorkspaces(),
			"space" => 140,
			"forceRightHeadline" => 1)
		);

		$button = we_button::create_button("ws_from_class", "javascript:we_cmd('ws_from_class');_EditorFrame.setEditorIsHot(true);");

		array_push($parts, array(
			"headline" => "",
			"html" => $button,
			"space" => 140)
		);
	} else{				 //	No workspaces defined
		array_push($parts, array(
			"headline" => "",
			"html" => g_l('modules_object', '[no_workspace_defined]'),
			"space" => 0)
		);
	}
}
print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("weOjFileProp", "100%", $parts, 30);
