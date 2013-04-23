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
function we_tag_search($attribs){

	$name = weTag_getAttribute('name', $attribs, '0');
	$type = weTag_getAttribute('type', $attribs);
	$xml = weTag_getAttribute('xml', $attribs);
	$value = weTag_getAttribute('value', $attribs);

	$searchValue = filterXss(str_replace(array('"', '\\"',), '', (isset($_REQUEST['we_lv_search_' . $name]) ? trim($_REQUEST['we_lv_search_' . $name]) : $value)));
	$attsHidden = array(
		'type' => 'hidden',
		'xml' => $xml,
		'name' => 'we_from_search_' . $name,
		'value' => (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'] ? 0 : 1)
	);


	switch($type){
		case 'print':
			return $searchValue;
		case 'textinput':
			$atts = removeAttribs($attribs, array(
				'type', 'onchange', 'name', 'cols', 'rows'
				));
			$atts = array_merge(
				$atts, array(
				'name' => 'we_lv_search_' . $name,
				'type' => 'text',
				'value' => $searchValue,
				'xml' => $xml
				));
			return getHtmlTag('input', $atts) . getHtmlTag('input', $attsHidden);

		case 'textarea':
			$atts = removeAttribs(
				$attribs, array(
				'type', 'onchange', 'name', 'size', 'maxlength', 'value'
				));
			$atts = array_merge(
				$atts, array(
				'class' => 'defaultfont',
				'name' => 'we_lv_search_' . $name,
				'xml' => $xml
				));

			return getHtmlTag('textarea', $atts, $searchValue, true) . getHtmlTag('input', $attsHidden);
	}
}