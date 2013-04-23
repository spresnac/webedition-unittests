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
class weCellDialog extends weDialog{

	var $JsOnly = true;
	var $changeableArgs = array("width",
		"height",
		"bgcolor",
		"background",
		"align",
		"valign",
		"colspan",
		"class",
		"isheader",
		"id",
		"headers",
		"scope"
	);

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', "[edit_cell]");
		$this->defaultInit();
	}

	function defaultInit(){
		$this->args["width"] = "";
		$this->args["height"] = "";
		$this->args["bgcolor"] = "";
		$this->args["background"] = "";
		$this->args["align"] = "";
		$this->args["valign"] = "";
		$this->args["colspan"] = "";
		$this->args["class"] = "";
		$this->args["isheader"] = "";
		$this->args["id"] = "";
		$this->args["headers"] = "";
		$this->args["scope"] = "";
	}

	function getDialogContentHTML(){

		$foo = $this->formColor(10, "we_dialog_args[bgcolor]", (isset($this->args["bgcolor"]) ? $this->args["bgcolor"] : ""), 50);
		$bgcolor = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[bgcolor]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[width]", 5, (isset($this->args["width"]) ? $this->args["width"] : ""), "", ' onkeypress="return IsDigitPercent(event);"', "text", 50);
		$width = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[width]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[height]", 5, (isset($this->args["height"]) ? $this->args["height"] : ""), "", ' onkeypress="return IsDigitPercent(event);"', "text", 50);
		$height = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[height]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[colspan]", 5, (isset($this->args["colspan"]) ? $this->args["colspan"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);

		$colspan = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[colspan]"));

		$foo = '<select class="defaultfont" name="we_dialog_args[align]" size="1">
							<option value="">Default</option>
							<option value="left"' . ((isset($this->args["align"]) && $this->args["align"] == "left") ? "selected" : "") . '>Left</option>
							<option value="center"' . ((isset($this->args["align"]) && $this->args["align"] == "center") ? "selected" : "") . '>Center</option>
							<option value="right"' . ((isset($this->args["align"]) && $this->args["align"] == "right") ? "selected" : "") . '>Right</option>
						</select>';
		$align = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[halignment]"));

		$foo = '<select class="defaultfont" name="we_dialog_args[valign]" size="1">
							<option value="">Default</option>
							<option value="top"' . ((isset($this->args["valign"]) && $this->args["valign"] == "top") ? "selected" : "") . '>Top</option>
							<option value="middle"' . ((isset($this->args["valign"]) && $this->args["valign"] == "middle") ? "selected" : "") . '>Middle</option>
							<option value="bottom"' . ((isset($this->args["valign"]) && $this->args["valign"] == "bottom") ? "selected" : "") . '>Bottom</option>
						</select>';
		$valign = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[valignment]"));

		$foo = we_html_element::jsElement( 'showclasss("we_dialog_args[class]","' . (isset($this->args["class"]) ? $this->args["class"] : "") . '","");');
		$classSelect = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[css_style]"));

		$_isheader = we_forms::checkboxWithHidden($this->args["isheader"] == 1, "we_dialog_args[isheader]", g_l('wysiwyg', "[isheader]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[id]", 5, (isset($this->args["id"]) ? $this->args["id"] : ""), "", '', "text", 50);
		$_id = we_html_tools::htmlFormElementTable($foo, "id");

		$foo = we_html_tools::htmlTextInput("we_dialog_args[headers]", 5, (isset($this->args["headers"]) ? $this->args["headers"] : ""), "", '', "text", 50);
		$_headers = we_html_tools::htmlFormElementTable($foo, "headers");

		$foo = '<select class="defaultfont" name="we_dialog_args[scope]" size="1">
							<option value="">Default</option>
							<option value="row"' . ((isset($this->args["scope"]) && $this->args["scope"] == "row") ? "selected" : "") . '>row</option>
							<option value="col"' . ((isset($this->args["scope"]) && $this->args["scope"] == "col") ? "selected" : "") . '>col</option>
							<option value="rowgroup"' . ((isset($this->args["scope"]) && $this->args["scope"] == "rowgroup") ? "selected" : "") . '>rowgroup</option>
							<option value="colgroup"' . ((isset($this->args["scope"]) && $this->args["scope"] == "colgroup") ? "selected" : "") . '>colgroup</option>
						</select>';
		$_scope = we_html_tools::htmlFormElementTable($foo, "scope");


		$table = '<table border="0" cellpadding="0" cellspacing="0">
<tr><td>' . $width . '</td><td>' . $height . '</td><td>' . $colspan . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td>' . $align . '</td><td>' . $valign . '</td><td>' . $bgcolor . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td>' . $_isheader . '</td><td>' . $_id . '</td><td>' . $_headers . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td colspan="3">' . $_scope . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td><td>' . we_html_tools::getPixel(135, 4) . '</td></tr>
<tr><td colspan="3">' . $classSelect . '</td></tr>
</table>
';

		return $table;
	}

	function getJs(){
		return parent::getJs() . we_html_element::jsElement('function showclasss(name, val, onCh) {' .
				((isset($this->args["cssClasses"]) && $this->args["cssClasses"]) ?
					'					var classCSV = "' . $this->args["cssClasses"] . '";
					classNames = classCSV.split(/,/);' :
					'					classNames = top.opener.we_classNames;') .
				'
	document.writeln(\'<select class="defaultfont"  name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onChange="\'+onCh+\'"\' : \'\')+\' style="width:380px">\');
	document.writeln(\'<option value="">' . g_l('wysiwyg', "[none]") . '\');
	if(typeof(classNames) != "undefined"){
		for(var i=0;i<classNames.length;i++){
			var foo = classNames[i].substring(0,1) == "." ?
								classNames[i].substring(1,classNames[i].length) :
								classNames[i];
			document.writeln(\'<option value="\'+foo+\'"\'+((val==foo) ? \' selected\' : \'\')+\'>\'+classNames[i]);
		}
	}
	document.writeln(\'</select>\');
}');
	}

}