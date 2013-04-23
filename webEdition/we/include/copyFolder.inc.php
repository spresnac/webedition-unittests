<?php

/**
 * webEdition CMS
 *
 * $Rev: 5075 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 02:04:23 +0100 (Tue, 06 Nov 2012) $
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
$yuiSuggest = & weSuggest::getInstance();

if(isset($_REQUEST['we_cmd'][3]) && $_REQUEST['we_cmd'][3]){

	$js = we_html_element::jsScript(JS_DIR . "windows.js") .
		we_html_element::jsElement(
			'self.focus();

		function removeAllCats(){
			if(categories_edit.itemCount>0){
				while(categories_edit.itemCount>0){
					categories_edit.delItem(categories_edit.itemCount);
				}
			}
		}

		function addCat(paths){
			var path = paths.split(",");
			var found = false;
			var j = 0;
			for (var i = 0; i < path.length; i++) {
				if(path[i]!="") {
					found = false;
					for(j=0;j<categories_edit.itemCount;j++){
						if(categories_edit.form.elements[categories_edit.name+"_variant0_"+categories_edit.name+"_item"+j].value == path[i]) {
							found = true;
						}
					}
					if(!found) {
						categories_edit.addItem();
						categories_edit.setItem(0,(categories_edit.itemCount-1),path[i]);
					}
				}
			}
			categories_edit.showVariant(0);
		}

		function we_cmd(){
			var args = "";
			var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

			switch (arguments[0]){
				case "openDirselector":
					new jsWindow(url,"we_fileselector",-1,-1,' . WINDOW_DIRSELECTOR_WIDTH . ',' . WINDOW_DIRSELECTOR_HEIGHT . ',true,true,true,true);
					break;
				case "openCatselector":
					new jsWindow(url,"we_cateditor",-1,-1,' . WINDOW_CATSELECTOR_WIDTH . ',' . WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
					break;
				default:
					for(var i = 0; i < arguments.length; i++){
						args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
					}
					eval("opener.we_cmd("+args+")");
			}
		}
		var lastCFolder;
		function toggleButton() {
			if(document.getElementById(\'CreateTemplate\').checked) {
				weButton.enable(\'select\');
				if(acin = document.getElementById(\'yuiAcInputTemplate\')) {
					document.getElementById(\'yuiAcInputTemplate\').disabled=false;
					lastCFolder = acin.value;
					acin.readOnly=false;
				}
				return true;
			} else {
				weButton.disable(\'select\');
				if(acin = document.getElementById(\'yuiAcInputTemplate\')) {
					document.getElementById(\'yuiAcInputTemplate\').disabled=true;
					acin.readOnly=true;
					acin.value = lastCFolder;
				}
				return true;
			}
			return false;
		}
		function incTemp(val) {
			if(val) {
				document.getElementsByName("CreateMasterTemplate")[0].disabled=false;
				document.getElementsByName("CreateIncludedTemplate")[0].disabled=false;
				document.getElementById("label_CreateMasterTemplate").style.color = "black";
				document.getElementById("label_CreateIncludedTemplate").style.color = "black";
			} else {
				document.getElementsByName("CreateMasterTemplate")[0].checked=false;
				document.getElementsByName("CreateIncludedTemplate")[0].checked=false;
				document.getElementsByName("CreateMasterTemplate")[0].disabled=true;
				document.getElementsByName("CreateIncludedTemplate")[0].disabled=true;
				document.getElementById("label_CreateMasterTemplate").style.color = "grey";
				document.getElementById("label_CreateIncludedTemplate").style.color = "grey";
			}
		}
		');

	$yes_button = we_button::create_button("ok", "form:we_form");
	$cancel_button = we_button::create_button("cancel", "javascript:self.close();");

	$pb = new we_progressBar(0);
	$pb->setStudLen(270);
	$pb->addText("&nbsp;", 0, "pbar1");
	$pbHTML = $pb->getHTML() . $pb->getJSCode();

	$buttons = '<table border="0" cellpadding="0" cellspacing="0" width="300"><tr><td align="left" id="pbTd" style="display:none;">' . $pbHTML . '</td><td align="right">' .
		we_button::position_yes_no_cancel($yes_button, null, $cancel_button) .
		'</td></tr></table>';
	$hidden =
		we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => $_REQUEST['we_cmd'][0])) .
		we_html_element::htmlHidden(array("name" => "we_cmd[1]", "value" => $_REQUEST['we_cmd'][1])) .
		we_html_element::htmlHidden(array("name" => "we_cmd[2]", "value" => $_REQUEST['we_cmd'][2])) .
		(isset($_REQUEST['we_cmd'][4]) ? we_html_element::htmlHidden(array("name" => "we_cmd[4]", "value" => $_REQUEST['we_cmd'][4])) : '');

	if(isset($_REQUEST['we_cmd'][4]) && defined('OBJECT_FILES_TABLE') && $_REQUEST['we_cmd'][4] == OBJECT_FILES_TABLE){
		$content = g_l('copyFolder', "[object_copy]") . '<br/>' .
			we_forms::checkbox("1", 0, "DoNotCopyFolders", g_l('copyFolder', "[object_copy_no_folders]")) .
			'&nbsp;<br/>' . g_l('copyFolder', "[sameName_headline]") . '<br/>' .
			we_html_tools::htmlAlertAttentionBox(g_l('copyFolder', "[sameName_expl]"), 2, 380) .
			we_html_tools::getPixel(200, 10) .
			we_forms::radiobutton("overwrite", 0, "OverwriteObjects", g_l('copyFolder', "[sameName_overwrite]")) .
			we_forms::radiobutton("rename", 0, "OverwriteObjects", g_l('copyFolder', "[sameName_rename]")) .
			we_forms::radiobutton("nothing", 1, "OverwriteObjects", g_l('copyFolder', "[sameName_nothing]")) .
			$hidden;
	} else{
		$content = '<table border="0" cellpadding="0" cellspacing="0" width="500"><tr><td>' . we_forms::checkbox(
				"1", 0, 'CreateTemplate', g_l('copyFolder', "[create_new_templates]"), false, "defaultfont", "toggleButton(); incTemp(this.checked)") . '
					<div id="imTemp" style="display:block">' . we_forms::checkbox(
				"1", 0, 'CreateMasterTemplate', g_l('copyFolder', "[create_new_masterTemplates]"), false, "defaultfont", "", 1) . we_forms::checkbox(
				"1", 0, 'CreateIncludedTemplate', g_l('copyFolder', "[create_new_includedTemplates]"), false, "defaultfont", "", 1) . '
					</div></td><td valign="top">' . we_forms::checkbox(
				"1", 0, 'CreateDoctypes', g_l('copyFolder', "[create_new_doctypes]")) . '
					</td></tr>
					<tr><td colspan="2">' . we_html_tools::getPixel(2, 5) . '</td></tr>
					<tr><td colspan="2">' . copyFolderFrag::formCreateTemplateDirChooser() . '</td></tr>
					<tr><td colspan="2">' . we_html_tools::getPixel(2, 5) . we_html_element::htmlBr() . copyFolderFrag::formCreateCategoryChooser() .
			$hidden .
			'</td></tr></table>';
	}
	copyFolderFrag::printHeader();
	print
		'<body class="weDialogBody">' . $js .
		'<form onsubmit="return fsubmit(this)" name="we_form" target="pbUpdateFrame" method="get">' .
		we_html_tools::htmlDialogLayout(
			$content, g_l('copyFolder', "[headline]") . ": " . shortenPath(
				id_to_path($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][4]), 46), $buttons
		) .
		'</form>' .
		'<iframe frameborder="0" src="about:blank" name="pbUpdateFrame" width="0" height="0" id="pbUpdateFrame"></iframe>' .
		$yuiSuggest->getYuiCss() .
		$yuiSuggest->getYuiJs();
	'</body></html>';
} else{

	$bodyAttribs =
		array(
			"bgcolor" => "#FFFFFF",
			"marginwidth" => 15,
			"marginheight" => 10,
			"leftmargin" => 15,
			"topmargin" => 10
	);
	$fr = (isset($_REQUEST["finish"]) ?
			new copyFolderFinishFrag("we_copyFolderFinish", 1, 0, $bodyAttribs) :
			new copyFolderFrag("we_copyFolder", 1, 0, $bodyAttribs)
		);
}
