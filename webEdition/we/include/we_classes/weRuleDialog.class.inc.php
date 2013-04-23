<?php

/**
 * webEdition CMS
 *
 * $Rev: 3750 $
 * $Author: mokraemer $
 * $Date: 2012-01-07 02:14:44 +0100 (Sat, 07 Jan 2012) $
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
class weRuleDialog extends weDialog{

	var $dialogWidth = 270;
	var $JsOnly = true;
	var $changeableArgs = array("width",
		"height",
		"color",
		"noshade",
		"align"
	);

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', "[edit_hr]");
		$this->defaultInit();
	}

	function defaultInit(){
		$this->args["width"] = "";
		$this->args["height"] = "";
		$this->args["color"] = "";
		$this->args["align"] = "";
		$this->args["noshade"] = false;
	}

	function getDialogContentHTML(){
		$foo = $this->formColor(7, "we_dialog_args[color]", (isset($this->args["color"]) ? $this->args["color"] : ""), 50);
		$color = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[color]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[width]", 5, (isset($this->args["width"]) ? $this->args["width"] : ""), "", ' onkeypress="return IsDigitPercent(event);"', "text", 50);
		$width = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[width]"));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[height]", 5, (isset($this->args["height"]) ? $this->args["height"] : ""), "", ' onkeypress="return IsDigitPercent(event);"', "text", 50);
		$height = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[height]"));

		$noshade = '<table cellpadding="0" cellspacing="0" border="0">
<tr><td><input type="checkbox" name="we_dialog_args[noshade]" value="1"' . ((isset($this->args["noshade"]) && $this->args["noshade"]) ? " checked" : "") . ' /></td><td>' . we_html_tools::getPixel(8, 2) . '</td><td class="defaultfont">' .
			g_l('wysiwyg', "[noshade]") . '</td></tr></table>';

		$foo = '<select class="defaultfont" name="we_dialog_args[align]" size="1">
							<option value="">Default</option>
							<option value="left"' . ((isset($this->args["align"]) && $this->args["align"] == "left") ? "selected" : "") . '>Left</option>
							<option value="center"' . ((isset($this->args["align"]) && $this->args["align"] == "center") ? "selected" : "") . '>Center</option>
							<option value="right"' . ((isset($this->args["align"]) && $this->args["align"] == "right") ? "selected" : "") . '>Right</option>
						</select>';
		$align = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[halignment]"));

		$table = '<table border="0" cellpadding="0" cellspacing="0">
<tr><td>' . $width . '</td><td>' . $height . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(90, 4) . '</td></tr>
<tr><td>' . $align . '</td><td>' . $color . '</td></tr>
<tr><td>' . we_html_tools::getPixel(135, 10) . '</td><td>' . we_html_tools::getPixel(90, 4) . '</td></tr>
<tr><td colspan="2">' . $noshade . '</td></tr>
</table>
';

		return $table;
	}

}