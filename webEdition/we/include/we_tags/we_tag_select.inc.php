<?php

/**
 * webEdition CMS
 *
 * $Rev: 5376 $
 * $Author: mokraemer $
 * $Date: 2012-12-18 11:43:24 +0100 (Tue, 18 Dec 2012) $
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
function we_tag_select($attribs, $content){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute("name", $attribs);
	$onchange = weTag_getAttribute("onchange", $attribs);
	$reload = weTag_getAttribute("reload", $attribs, false, true);
	if($GLOBALS['we_editmode']){
		$val = $GLOBALS['we_doc']->getElement($name);
		$attr = we_make_attribs($attribs, "name,value,onchange,_name_orig");
		$content = preg_replace('|<(option[^>]*) selected( *=? *"selected")?([^>]*)>|i', "<\\1\\3>", $content);
		if(stripos($content, '<option>') !== false){
			$content = preg_replace('|<option>' . preg_quote($val) . "( ?[<\n\r\t])|i", '<option selected="selected">' . $val . '\\1', $content);
		}
		if(preg_match('|<option[^>]*value=[\'"]?.*[\'"]?>|i', $content)){
			$content = preg_replace('|<option([^>]*)value *= *"' . preg_quote($val) . '"([^>]*)>|i', '<option value="' . $val . '" selected="selected">', $content);
		}
		return '<select onchange="_EditorFrame.setEditorIsHot(true);' . ($onchange ? $onchange : "") . ';' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" class="defaultfont" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" ' . ($attr ? " $attr" : "") . '>' . $content . '</select>';
	} else{
		return $GLOBALS['we_doc']->getElement($name);
	}
}
