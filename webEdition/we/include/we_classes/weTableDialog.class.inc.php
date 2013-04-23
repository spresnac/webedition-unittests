<?php

/**
 * webEdition CMS
 *
 * $Rev: 5185 $
 * $Author: mokraemer $
 * $Date: 2012-11-20 22:07:11 +0100 (Tue, 20 Nov 2012) $
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
class weTableDialog extends weDialog{

	var $ClassName = __CLASS__;
	var $JsOnly = true;
	var $changeableArgs = array("border",
		"rows",
		"cols",
		"width",
		"height",
		"bgcolor",
		"background",
		"cellspacing",
		"cellpadding",
		"align",
		"class",
		"summary"
	);

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', "[edit_table]");
		$this->defaultInit();
	}

	function defaultInit(){
		$this->args["border"] = "";
		$this->args["rows"] = "";
		$this->args["cols"] = "";
		$this->args["width"] = "";
		$this->args["height"] = "";
		$this->args["bgcolor"] = "";
		$this->args["background"] = "";
		$this->args["cellpadding"] = "";
		$this->args["cellpadding"] = "";
		$this->args["align"] = "";
		$this->args["class"] = "";
		$this->args["summary"] = "";
	}

	function getDialogContentHTML(){

		$foo = $this->formColor(10, "we_dialog_args[bgcolor]", (isset($this->args["bgcolor"]) ? $this->args["bgcolor"] : ""), 50);
		$bgcolor = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[bgcolor]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[cellspacing]", 5, (isset($this->args["cellspacing"]) ? $this->args["cellspacing"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);
		$cellspacing = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[cellspacing]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[cellpadding]", 5, (isset($this->args["cellpadding"]) ? $this->args["cellpadding"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);

		$cellpadding = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[cellpadding]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[border]", 5, (isset($this->args["border"]) ? $this->args["border"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);

		$border = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[border]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[cols]", 5, (isset($this->args["cols"]) ? $this->args["cols"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);

		$cols = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[cols]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[rows]", 5, (isset($this->args["rows"]) ? $this->args["rows"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);

		$rows = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[rows]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[width]", 5, (isset($this->args["width"]) ? $this->args["width"] : ""), "", ' onkeypress="return IsDigitPercent(event);"', "text", 50);
		$width = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[width]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[height]", 5, (isset($this->args["height"]) ? $this->args["height"] : ""), "", ' onkeypress="return IsDigitPercent(event);"', "text", 50);
		$height = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[height]"));


		$foo = we_html_tools::htmlTextInput("we_dialog_args[summary]", 50, (isset($this->args["summary"]) ? $this->args["summary"] : ""), "", '', "text", 380);
		$_summary = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[summary]"));

		$foo = '<select class="defaultfont" name="we_dialog_args[align]" size="1" style="width:110px">
							<option value="">Default</option>
							<option value="top"' . ((isset($this->args["align"]) && $this->args["align"] == "top") ? "selected" : "") . '>Top</option>
							<option value="center"' . ((isset($this->args["align"]) && $this->args["align"] == "center") ? "selected" : "") . '>Center</option>
							<option value="bottom"' . ((isset($this->args["align"]) && $this->args["align"] == "bottom") ? "selected" : "") . '>Bottom</option>
							<option value="left"' . ((isset($this->args["align"]) && $this->args["align"] == "left") ? "selected" : "") . '>Left</option>
							<option value="right"' . ((isset($this->args["align"]) && $this->args["align"] == "right") ? "selected" : "") . '>Right</option>
							<option value="texttop"' . ((isset($this->args["align"]) && $this->args["align"] == "texttop") ? "selected" : "") . '>Text Top</option>
							<option value="baseline"' . ((isset($this->args["align"]) && $this->args["align"] == "baseline") ? "selected" : "") . '>Baseline</option>
							<option value="absbottom"' . ((isset($this->args["align"]) && $this->args["align"] == "absbottom") ? "selected" : "") . '>Abs Bottom</option>
						</select>';
		$align = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[alignment]"));

		$foo = we_html_element::jsElement('showclasss("we_dialog_args[class]","' . (isset($this->args["class"]) ? $this->args["class"] : "") . '","");');
		$classSelect = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[css_style]"));

		$table = '<table border="0" cellpadding="0" cellspacing="0">
<tr><td>' . $rows . '</td><td>' . $cols . '</td><td>' . $border . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td>' . $cellpadding . '</td><td>' . $cellspacing . '</td><td>' . $bgcolor . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td>' . $width . '</td><td>' . $height . '</td><td>' . $align . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td colspan="3">' . $_summary . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td colspan="3">' . $classSelect . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
</table>
';

		return $table;
	}

	function getJs(){
		return parent::getJs() . we_html_element::jsElement('
				function showclasss(name, val, onCh) {'.
		(isset($this->args["cssClasses"]) && $this->args["cssClasses"]?
			'					var classCSV = "' . $this->args["cssClasses"] . '";
					classNames = classCSV.split(/,/);':

			'					classNames = top.opener.we_classNames;').

		'
					document.writeln(\'<select class="defaultfont" style="width:380px" name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onChange="\'+onCh+\'"\' : \'\')+\'>\');
					document.writeln(\'<option value="">' . g_l('wysiwyg', "[none]") . '\');
					if(typeof(classNames) != "undefined"){
						for (var i = 0; i < classNames.length; i++) {
							var foo = classNames[i].substring(0,1) == "." ?
								classNames[i].substring(1,classNames[i].length) :
								classNames[i];
							document.writeln(\'<option value="\'+foo+\'"\'+((val==foo) ? \' selected\' : \'\')+\'>.\'+foo);
						}
					}
					document.writeln(\'</select>\');
				}');
	}

}