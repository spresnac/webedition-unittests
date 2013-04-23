<?php

/**
 * webEdition CMS
 *
 * $Rev: 5906 $
 * $Author: lukasimhof $
 * $Date: 2013-03-01 14:08:18 +0100 (Fri, 01 Mar 2013) $
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


$nr = isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : "";
$name = isset($_REQUEST['we_cmd'][2]) ? $_REQUEST['we_cmd'][2] : "";
$we_transaction = isset($_REQUEST['we_cmd'][3]) ? $_REQUEST['we_cmd'][3] : "";
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : '');

$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : "";
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

if(isset($_REQUEST["ok"])){
	$we_doc->elements[$name . "inlineedit"]["dat"] = isset($_REQUEST["inlineedit"]) ? $_REQUEST["inlineedit"] : "";
	$we_doc->elements[$name . "forbidphp"]["dat"] = isset($_REQUEST["forbidphp"]) ? $_REQUEST["forbidphp"] : "";
	$we_doc->elements[$name . "forbidhtml"]["dat"] = isset($_REQUEST["forbidhtml"]) ? $_REQUEST["forbidhtml"] : false;
	$we_doc->elements[$name . "removefirstparagraph"]["dat"] = isset($_REQUEST["removefirstparagraph"]) ? $_REQUEST["removefirstparagraph"] : "";
	$we_doc->elements[$name . "xml"]["dat"] = isset($_REQUEST["xml"]) ? $_REQUEST["xml"] : "";
	$we_doc->elements[$name . "dhtmledit"]["dat"] = isset($_REQUEST["dhtmledit"]) ? $_REQUEST["dhtmledit"] : "";
	$we_doc->elements[$name . "showmenus"]["dat"] = isset($_REQUEST["showmenus"]) ? $_REQUEST["showmenus"] : "";
	$we_doc->elements[$name . "commands"]["dat"] = isset($_REQUEST["commands"]) ? $_REQUEST["commands"] : "";
	$we_doc->elements[$name . "height"]["dat"] = isset($_REQUEST["height"]) ? $_REQUEST["height"] : 50;
	$we_doc->elements[$name . "width"]["dat"] = isset($_REQUEST["width"]) ? $_REQUEST["width"] : 200;
	$we_doc->elements[$name . "bgcolor"]["dat"] = isset($_REQUEST["bgcolor"]) ? $_REQUEST["bgcolor"] : '';
	$we_doc->elements[$name . "class"]["dat"] = isset($_REQUEST["class"]) ? $_REQUEST["class"] : '';
	$we_doc->elements[$name . "cssClasses"]["dat"] = isset($_REQUEST["cssClasses"]) ? $_REQUEST["cssClasses"] : "";
	$we_doc->elements[$name . "tinyparams"]["dat"] = isset($_REQUEST["tinyparams"]) ? $_REQUEST["tinyparams"] : '';
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
}

if(isset($_REQUEST["ok"])){
	$js = 'opener._EditorFrame.setEditorIsHot(true);'
		. 'opener.we_cmd("reload_entry_at_class","' . $we_transaction . '", "' . $nr . '");'
		. 'top.close();';
} else{
	$js = 'function okFn(){'
		. 'document.forms[0].submit();'
		. '}';
}

print we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead(//FIXME: missing title
			we_html_tools::getHtmlInnerHead() . we_html_element::jsElement($js) . STYLESHEET), false);

$out = '<body onload="top.focus();" class="weDialogBody"><form name="we_form" method="post" action="' . $_SERVER['SCRIPT_NAME'] . '"><input type="hidden" name="ok" value="1" />';

foreach($_REQUEST['we_cmd'] as $k => $v){
	$out .= '<input type="hidden" name="we_cmd[' . $k . ']" value="' . $v . '" />';
}

$parts = array();

// WYSIWYG && FORBIDHTML && FORBIDPHP
$vals = array('off' => 'false', 'on' => 'true');
$selected = (isset($we_doc->elements[$name . "dhtmledit"]) && isset($we_doc->elements[$name . "dhtmledit"]["dat"]) && $we_doc->elements[$name . "dhtmledit"]["dat"] == "on") ? 'on' : 'off';
$wysiwyg = we_html_tools::htmlSelect("dhtmledit", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$vals = array('off' => 'false', 'on' => 'true');
$selected = (isset($we_doc->elements[$name . "forbidhtml"]) && isset($we_doc->elements[$name . "forbidhtml"]["dat"]) && $we_doc->elements[$name . "forbidhtml"]["dat"] == "on") ? 'on' : 'off';
$forbidhtml = we_html_tools::htmlSelect("forbidhtml", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$vals = array('off' => 'false', 'on' => 'true');
$selected = ( (!isset($we_doc->elements[$name . "forbidphp"]["dat"])) || $we_doc->elements[$name . "forbidphp"]["dat"] == "on" ? 'on' : 'off');
$forbidphp = we_html_tools::htmlSelect("forbidphp", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">wysiwyg&nbsp;</td><td>' . $wysiwyg . '</td>
		<td class="defaultfont" align="right">forbidphp&nbsp;</td><td>' . $forbidphp . '</td>
		<td class="defaultfont" align="right">forbidhtml&nbsp;</td><td>' . $forbidhtml . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

// XML && REMOVEFIRSTPARAGRAPH
$vals = array('off' => 'false', 'on' => 'true');
$selected = ( (!isset($we_doc->elements[$name . "xml"]["dat"])) || $we_doc->elements[$name . "xml"]["dat"] == "on" ? 'on' : 'off');
$xml = we_html_tools::htmlSelect("xml", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$vals = array('off' => 'false', 'on' => 'true');
$selected = ( (!isset($we_doc->elements[$name . "removefirstparagraph"]["dat"])) || $we_doc->elements[$name . "removefirstparagraph"]["dat"] == "on" ? 'on' : 'off');
$removefirstparagraph = we_html_tools::htmlSelect("removefirstparagraph", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">xml&nbsp;</td><td>' . $xml . '</td>
		<td class="defaultfont" align="right"></td><td></td>
		<td class="defaultfont" align="right">removefirstparagraph&nbsp;</td><td>' . $removefirstparagraph . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);


// INLINEEDIT && SHOWMENUS
$vals = array('off' => 'false', 'on' => 'true');
$selected = ( (!isset($we_doc->elements[$name . "inlineedit"]["dat"])) || $we_doc->elements[$name . "inlineedit"]["dat"] == "on" ? 'on' : 'off');
$inlineedit = we_html_tools::htmlSelect("inlineedit", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$vals = array('off' => 'false', 'on' => 'true');
$selected = ( (!isset($we_doc->elements[$name . "showmenus"]["dat"])) || $we_doc->elements[$name . "showmenus"]["dat"] == "on" ? 'on' : 'off');
$showmenus = we_html_tools::htmlSelect("showmenus", $vals, 1, $selected, false, 'class="defaultfont"', 'value', 60);

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">inlineedit&nbsp;</td><td>' . $inlineedit . '</td>
		<td class="defaultfont" align="right"></td><td></td>
		<td class="defaultfont" align="right">showmenus&nbsp;</td><td>' . $showmenus . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);


// WIDTH & HEIGHT & BGCOLOR
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">width&nbsp;</td><td>' . we_html_tools::htmlTextInput('width', 24, $we_doc->elements[$name . "width"]["dat"], 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" align="right">height&nbsp;</td><td>' . we_html_tools::htmlTextInput('height', 24, $we_doc->elements[$name . "height"]["dat"], 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" align="right">bgcolor&nbsp;</td><td>' . we_html_tools::htmlTextInput('bgcolor', 24, $we_doc->elements[$name . "bgcolor"]["dat"], 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" align="right"></td><td></td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 6) . '</td>
		<td>' . we_html_tools::getPixel(60, 6) . '</td>
		<td>' . we_html_tools::getPixel(95, 6) . '</td>
		<td>' . we_html_tools::getPixel(60, 6) . '</td>
		<td>' . we_html_tools::getPixel(140, 6) . '</td>
		<td>' . we_html_tools::getPixel(60, 6) . '</td>
	</tr>
	<tr>
		<td class="defaultfont" align="right">class&nbsp;</td><td>' . we_html_tools::htmlTextInput('class', 24, $we_doc->elements[$name . "class"]["dat"], 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" align="right"></td><td></td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);


// CLASSES
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">classes&nbsp;</td><td colspan="5">' . we_class::htmlTextArea("cssClasses", 3, 30, oldHtmlspecialchars((isset($we_doc->elements[$name . "cssClasses"]["dat"]) ? $we_doc->elements[$name . "cssClasses"]["dat"] : "")), 'style="width:415px;height:50px"') . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(415, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

// COMMANDS
$vals = makeArrayFromCSV(",," . WE_WYSIWYG_COMMANDS);
sort($vals);
$select = we_html_tools::htmlSelect("tmp_commands", $vals, 1, "", false, 'onchange="var elem=document.getElementById(\'commands\'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(\',\').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + \',\' + txt) : txt;}this.selectedIndex=-1"');

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">commands&nbsp;</td><td colspan="5">' . $select . '<br>' . we_class::htmlTextArea("commands", 3, 30, oldHtmlspecialchars((isset($we_doc->elements[$name . "commands"]["dat"]) ? $we_doc->elements[$name . "commands"]["dat"] : "")), 'id="commands" style="width:415px;height:50px"') . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(415, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

// TINYPARAMS
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">tinyparams&nbsp;</td><td colspan="5">' . we_html_tools::htmlTextInput('tinyparams', 24, $we_doc->elements[$name . "tinyparams"]["dat"], 1024, '', 'text', 350, 0) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(415, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

$cancel_button = we_button::create_button("cancel", "javascript:top.close()");
$okbut = we_button::create_button("ok", "javascript:okFn();");
$buttons = we_button::position_yes_no_cancel($okbut, null, $cancel_button);
$out .= we_multiIconBox::getHTML("", "100%", $parts, 30, $buttons, -1, "", "", "", g_l('modules_object', '[textarea_field]') . ' "' . $we_doc->elements[$name]['dat'] . '" - ' . g_l('modules_object', '[attributes]')) .
	'</form></body></html>';

print $out;
