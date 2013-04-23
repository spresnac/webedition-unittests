<?php

/**
 * webEdition CMS
 *
 * $Rev: 4598 $
 * $Author: mokraemer $
 * $Date: 2012-06-17 02:02:35 +0200 (Sun, 17 Jun 2012) $
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
class weColorDialog extends weDialog{

	var $changeableArgs = array("color");
	var $JsOnly = true;

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', "[choosecolor]");
		$this->args["color"] = "";
	}

	function getDialogContentHTML(){

		$colortable = '<table border="1" bordercolor="SILVER" bordercolorlight="WHITE" bordercolordark="BLACK" cellspacing="0" cellpadding="0">
<script  type="text/javascript">
var z=0;
for ( col in we_color2 ){
	if(z == 0){
		document.writeln(\'<tr>\');
	}

document.writeln(\'<td bgcolor="\'+col+\'"><a href="#" onClick="selectColor(\\\'\'+col+\'\\\');"><span style="display:inline-block;width:15px;height:15px" title="\'+we_color2[col]+\'"></span></a></td>\');

if(z==17){
		document.writeln(\'</tr>\');
		z = 0;
	}else{
		z++;
	}
}
if(z != 0){
	for(var i=z;i<18;i++){
		document.writeln(\'<td></td>\');
	}
	document.writeln(\'</tr>\');
}
</script>
		</table>
	';
		$trash = we_button::create_button("image:btn_function_trash", "javascript:selectColor('')");

		$foo = '<table border="0" cellpadding="0" cellspacing="0"><tr><td><input type="text" size="20" name="we_dialog_args[color]" class="defaultfont" style="width:150px;' . ($this->args["color"] ? ('background-color:' . $this->args["color"] . ';') : '') . '" value="' . $this->args["color"] . '" /></td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $trash . '</td></tr></table>';
		$color = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[color]"));


		$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . $colortable . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(2, 10) . '</td>
	</tr>
	<tr>
		<td>' . $color . '</td>
	</tr>
</table>
';
		return $table;
	}

	function getJs(){
		return weDialog::getJs() . we_html_element::jsScript(JS_DIR . 'we_colors2.js') .we_html_element::jsElement( '
function selectColor(c){
	document.we_form.elements["we_dialog_args[color]"].value = c;
	if(document.we_form.elements["we_dialog_args[color]"].style){
		document.we_form.elements["we_dialog_args[color]"].style.backgroundColor = c;
	}
}');
	}

}