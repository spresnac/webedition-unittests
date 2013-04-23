<?php

/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
class weImportRtfDialog extends weDialog{

	var $ClassName = __CLASS__;
	var $pageNr = 1;
	var $numPages = 2;
	var $JsOnly = true;
	var $arg = array();
	var $changeableArgs = array("htmltxt",
		"applyFontName",
		"applyFontSize",
		"applyFontColor"
	);

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('importrtf', "[import_rtf]");
		$this->args["htmltxt"] = "";
		$this->args["applyFontName"] = false;
		$this->args["applyFontSize"] = false;
		$this->args["applyFontColor"] = false;
	}

	function getJs(){
		return weDialog::getJs() . we_html_element::jsElement('
function checkTheBox(box){
	b = document.we_form.elements[box];
	b.checked = (b.checked) ? false : true;
}

function importFile(){
	f = document.we_form;
	f.we_what.value = "dialog";
	f.submit();
}');
	}

	function getNextBut(){
		return we_button::create_button("next", "javascript:importFile();");
	}

	function getHTML(){
		if($this->pageNr == 2)
			$this->JsOnly = true;
		return parent::getHTML();
	}

	function getFormHTML(){
		if($this->pageNr == 1){
			return '<form enctype="multipart/form-data" name="we_form" action="' . $_SERVER["SCRIPT_NAME"] . '" method="post" target="_self">';
		} else{
			return '<form name="we_form" action="' . $_SERVER["SCRIPT_NAME"] . '" method="post" target="we_' . $this->ClassName . '_cmd_frame">';
		}
	}

	function getDialogContentHTML(){

		switch($this->pageNr){
########################################################################
			case 1:
########################################################################
				$content = '<table border="0" cellpadding="0" cellspacing="0" width="550">
	<tr>
		<td>' . we_html_tools::getPixel(550, 5) . '</td>
	</tr>
	<tr>
		<td class="defaultfont"><b>' . g_l('importrtf', "[chose]") . '</b></td>
	</tr>
	<tr>
		<td><input type="file" name="fileName" size="50" onKeyDown="return false" /></td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(5, 10) . '</td>
	</tr>
	<tr>
		<td>' . we_forms::checkbox("1", (isset($this->args["applyFontName"]) && $this->args["applyFontName"] == 1), "we_dialog_args[applyFontName]", g_l('importrtf', "[use_fontname]")) . '</td>
	</tr>
	<tr>
		<td>' . we_forms::checkbox("1", (isset($this->args["applyFontSize"]) && $this->args["applyFontSize"] == 1), "we_dialog_args[applyFontSize]", g_l('importrtf', "[use_fontsize]")) . '</td>
	</tr>
	<tr>
		<td>' . we_forms::checkbox("1", (isset($this->args["applyFontColor"]) && $this->args["applyFontColor"] == 1), "we_dialog_args[applyFontColor]", g_l('importrtf', "[use_fontcolor]")) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(5, 22) . '</td>
	</tr>
</table><input type="hidden" name="we_pageNr" value="2" />
';
				break;
########################################################################
			case 2:
########################################################################
				if(isset($_FILES["fileName"]) && is_array($_FILES["fileName"])){

					$filename = isset($_FILES["fileName"]["tmp_name"]) ? $_FILES["fileName"]["tmp_name"] : "";
					if($filename && $filename != "none"){

						$this->args["applyFontName"] = isset($this->args["applyFontName"]) ? $this->args["applyFontName"] : false;
						$this->args["applyFontSize"] = isset($this->args["applyFontSize"]) ? $this->args["applyFontSize"] : false;
						$this->args["applyFontColor"] = isset($this->args["applyFontColor"]) ? $this->args["applyFontColor"] : false;

						$rtf2html = new we_rtf2html($filename, $this->args["applyFontName"], $this->args["applyFontSize"], $this->args["applyFontColor"]);
					}
				}
				$content = '<table border="0" cellpadding="0" cellspacing="0" width="550">
	<tr>
		<td colspan="2" class="defaultfont"><b>' . g_l('global', "[preview]") . '</b></td>
	</tr>

	<tr>
		<td colspan="2"><textarea id="we_dialog_args[htmltxt]" name="we_dialog_args[htmltxt]" cols="59" rows="15" style="width:550px">' .
					(isset($rtf2html) ? oldHtmlspecialchars($rtf2html->htmlOut) : "") . '</textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">' . we_html_tools::getPixel(5, 22) . '</td>
	</tr>
</table>
';
				break;
########################################################################
		}
		return $content;
	}

}
