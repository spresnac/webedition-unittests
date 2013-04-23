<?php

/**
 * webEdition CMS
 *
 * $Rev: 5100 $
 * $Author: lukasimhof $
 * $Date: 2012-11-08 15:01:49 +0100 (Thu, 08 Nov 2012) $
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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");
if(!(isset($_REQUEST['we_dialog_args']) && isset($_REQUEST['we_dialog_args']['outsideWE']) && $_REQUEST['we_dialog_args']['outsideWE']==1) ){
	we_html_tools::protect();
}
$appendJS = "";
if(defined("GLOSSARY_TABLE") && isset($_REQUEST['weSaveToGlossary']) && $_REQUEST['weSaveToGlossary'] == 1){
	$Glossary = new weGlossary();
	$Glossary->Language = $_REQUEST['language'];
	$Glossary->Type = "foreignword";
	$Glossary->Text = trim($_REQUEST['text']);
	$Glossary->Published = time();
	$Glossary->setAttribute('lang', $_REQUEST['we_dialog_args']['lang']);
	$Glossary->setPath();

	if($Glossary->Text == "" || $Glossary->getAttribute('lang') == ""){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
	} else if($Glossary->pathExists($Glossary->Path)){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
	} else{
		$Glossary->save();
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[entry_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . 'top.close();');
	}
}

$dialog = new weLangDialog();
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoLangJS");
print $dialog->getHTML();
print $appendJS;

function weDoLangJS(){
	return '
if(typeof(isTinyMCE) != "undefined" && isTinyMCE === true){	
	WelangDialog.insert();
	top.close();
} else{
	eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
	var lang = document.we_form.elements["we_dialog_args[lang]"].value;
	editorObj.editLang(lang);
	top.close();
}';
}