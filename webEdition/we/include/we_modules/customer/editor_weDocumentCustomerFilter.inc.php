<?php

/**
 * webEdition CMS
 *
 * $Rev: 5458 $
 * $Author: mokraemer $
 * $Date: 2012-12-27 02:30:57 +0100 (Thu, 27 Dec 2012) $
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
$parts = array();
$_space_size = 120;


if($we_doc->ClassName != "we_imageDocument" && we_hasPerm("CAN_EDIT_CUSTOMERFILTER")){
	$_filter = $we_doc->documentCustomerFilter;
	if(!$_filter){
		$_filter = weDocumentCustomerFilter::getEmptyDocumentCustomerFilter();
	}
	$_view = new weDocumentCustomerFilterView($_filter, "_EditorFrame.setEditorIsHot(true);", 520);

	$parts[] = array(
		'headline' => g_l('modules_customerFilter', '[customerFilter]'),
		'html' => $_view->getFilterHTML(),
		'space' => $_space_size
	);
}


$parts[] = array(
	'headline' => g_l('modules_customer', '[one_customer]'),
	'html' => formWebuser(we_hasPerm("CAN_CHANGE_DOCS_CUSTOMER"), 434),
	'space' => $_space_size
);



print we_html_tools::htmlTop() .
	STYLESHEET;
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print we_html_element::cssElement("
.paddingLeft {
	padding-left: 25px;
}
.paddingVertical {
	padding-top: 10px;
	padding-bottom: 10px;
}

");

print we_html_element::jsScript(JS_DIR . "windows.js") .
	we_html_element::jsScript(JS_DIR . "utils/multi_edit.js") .
	(isset($yuiSuggest) ? // webuser filter is not displayed at images, so $yuiSuggest is not defined!
		$yuiSuggest->getYuiCssFiles() . $yuiSuggest->getYuiJsFiles() : '') .
	'</head><body class="weEditorBody"><form name="we_form" onsubmit="return false">' .
	we_class::hiddenTrans() .
	($we_doc->ClassName != "we_imageDocument" ?
		we_html_tools::hidden("we_edit_weDocumentCustomerFilter", 1) .
		we_html_tools::hidden("weDocumentCustomerFilter_id", $_filter->getId()) : '') .
	we_multiIconBox::getHTML("weDocProp", "100%", $parts, 20, "", -1, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]")) .
	'</form>
</body>
</html>';

function formWebuser($canChange, $width = 388){
	if(!$GLOBALS['we_doc']->WebUserID)
		$GLOBALS['we_doc']->WebUserID = 0;

	$webuser = ""; //g_l('weClass',"[nobody]");

	if($GLOBALS['we_doc']->WebUserID != 0){
		$webuser = id_to_path($GLOBALS['we_doc']->WebUserID, CUSTOMER_TABLE);
		if(!$webuser){
			$webuser = ""; //g_l('weClass',"[nobody]");
		}
	}

	if(!$canChange){
		return $webuser;
	}

	$textname = 'wetmp_' . $GLOBALS['we_doc']->Name . '_WebUserID';
	$idname = 'we_' . $GLOBALS['we_doc']->Name . '_WebUserID';

	//$attribs = ' readonly';
	//$inputFeld=$GLOBALS['we_doc']->htmlTextInput($textname,24,$webuser,"",$attribs,"",$width);
	//$idfield = $GLOBALS['we_doc']->htmlHidden($idname,$GLOBALS['we_doc']->WebUserID);

	$button = we_button::create_button("select", "javascript:we_cmd('openSelector',document.we_form.elements['$idname'].value,'" . CUSTOMER_TABLE . "','document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$textname\\'].value')");

	$_trashBut = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['$idname'].value=0;document.we_form.elements['$textname'].value='';_EditorFrame.setEditorIsHot(true);");
	/*
	  $out = $GLOBALS['we_doc']->htmlFormElementTable($inputFeld,
	  g_l('modules_customer','[connected_with_customer]'),
	  "left",
	  "defaultfont",
	  $idfield,
	  we_html_tools::getPixel(20,4),
	  $button,we_html_tools::getPixel(5,4),$_trashBut);
	 */
	$yuiSuggest = & weSuggest::getInstance();
	$yuiSuggest->setAcId("Customer");
	$yuiSuggest->setContentType("");
	$yuiSuggest->setInput($textname, $webuser, '', '', 1);
	$yuiSuggest->setLabel(g_l('modules_customer', '[connected_with_customer]'));
	$yuiSuggest->setMaxResults(20);
	$yuiSuggest->setMayBeEmpty(true);
	$yuiSuggest->setResult($idname, $GLOBALS['we_doc']->WebUserID);
	$yuiSuggest->setSelector("Docselector");
	$yuiSuggest->setWidth(434);
	$yuiSuggest->setSelectButton($button);
	$yuiSuggest->setTrashButton($_trashBut);
	$yuiSuggest->setTable(CUSTOMER_TABLE);

	return $yuiSuggest->getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiCode();
}
