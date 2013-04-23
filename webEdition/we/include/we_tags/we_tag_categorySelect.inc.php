<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_tag_categorySelect($attribs, $content){
	$name = weTag_getAttribute('name', $attribs);
	$isuserinput = (strlen($name) == 0);
	$name = $isuserinput ? 'we_ui_' . $GLOBALS['WE_FORM'] . '_categories' : $name;

	$type = weTag_getAttribute('type', $attribs);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$firstentry = weTag_getAttribute('firstentry', $attribs);
	$showpath = weTag_getAttribute('showpath', $attribs, false, true);
	$indent = weTag_getAttribute('indent', $attribs);
	$multiple = weTag_getAttribute('multiple', $attribs, false, true);

	$values = '';
	if($isuserinput && $GLOBALS['WE_FORM']){
		$objekt = isset($GLOBALS['we_object'][$GLOBALS['WE_FORM']]) ?
			$GLOBALS['we_object'][$GLOBALS['WE_FORM']] :
			(isset($GLOBALS['we_document'][$GLOBALS['WE_FORM']]) ?
				$GLOBALS['we_document'][$GLOBALS['WE_FORM']] :
				(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] :
					false));
		if($objekt){
			$values = $objekt->Category;
		}
		$valuesArray = makeArrayFromCSV(id_to_path($values, CATEGORY_TABLE));
	} else{
		if($type == 'request'){
			// Bug Fix #750
			$values = filterXss(isset($_REQUEST[$name]) ?
					(is_array($_REQUEST[$name]) ?
						implode(',', $_REQUEST[$name]) :
						$_REQUEST[$name]) :
					'');
		} else{
			// Bug Fix #750
			$values = (isset($GLOBALS[$name]) && is_array($GLOBALS[$name])) ?
				implode(',', $GLOBALS[$name]) :
				$GLOBALS[$name];
		}
		$valuesArray = makeArrayFromCSV($values, CATEGORY_TABLE);
	}

	$attribs['name'] = $name;

	// Bug Fix #750
	if($multiple){
		$attribs['name'] .= '[]';
		$attribs['multiple'] = 'multiple';
	} else{
		$attribs = removeAttribs($attribs, array('size', 'multiple'));
	}

	$attribs = removeAttribs($attribs, array('showpath', 'rootdir', 'firstentry', 'type'));

	$content = trim($content);
	if(!$content){
		if($firstentry){
			$content .= getHtmlTag('option', array('value' => ''), $firstentry);
		}
		$db = new DB_WE();
		$dbfield = $showpath || $indent ? 'Path' : 'Category';
		$db->query('SELECT Path,Category FROM ' . CATEGORY_TABLE . ' WHERE Path LIKE "' . $db->escape($rootdir) . '%" ORDER BY ' . $dbfield);
		while($db->next_record()) {
			$deep = count(explode('/', $db->f('Path'))) - 2;
			$field = $db->f($dbfield);
			if($rootdir && ($rootdir != '/') && $showpath){
				$field = preg_replace('|^' . preg_quote($rootdir) . '|', '', $field);
			}
			if($field){
				if(in_array($db->f('Path'), $valuesArray)){
					$content .= getHtmlTag('option', array(
						'value' => $db->f('Path'), 'selected' => 'selected'
						), str_repeat($indent, $deep) . $field);
				} else{
					$content .= getHtmlTag('option', array(
						'value' => $db->f('Path')
						), str_repeat($indent, $deep) . $field);
				}
			}
		}
	} else{
		foreach($valuesArray as $catPaths){
			if(stripos($content, '<option>') !== false){
				$content = preg_replace('/<option>' . preg_quote($catPaths) . '( ?[<\n\r\t])/i', '<option selected="selected">' . $catPaths . '\1', $content);
			}
			$content = str_replace('<option value="' . $catPaths . '">', '<option value="' . $catPaths . '" selected="selected">', $content);
		}
	}
	return getHtmlTag('select', $attribs, $content, true);
}
