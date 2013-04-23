<?php

/**
 * webEdition CMS
 *
 * $Rev: 5979 $
 * $Author: lukasimhof $
 * $Date: 2013-03-21 14:45:54 +0100 (Thu, 21 Mar 2013) $
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
function we_parse_tag_title($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('title', $attribs, $content, true) . ');?>';
}

function we_tag_title($attribs, $content){
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
	$prefix = weTag_getAttribute('prefix', $attribs);
	$suffix = weTag_getAttribute('suffix', $attribs);
	$delimiter = weTag_getAttribute('delimiter', $attribs);

	$attribs = removeAttribs($attribs, array('htmlspecialchars', 'prefix', 'suffix', 'delimiter'));
	if($GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS['we_doc']->InWebEdition){ //	normally meta tags are edited on property page
		return '<?php	$GLOBALS["meta"]["Title"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	} else{
		$title = isset($GLOBALS['TITLE']) && $GLOBALS['TITLE'] ? $GLOBALS['TITLE'] : $content;
		$title = ($prefix != '' ? $prefix . ($title != '' ? $delimiter : '') : '') . $title . ($suffix != '' ? ($title != '' ? $delimiter : ($prefix != '' ? $delimiter : '')) . $suffix : '');
		return getHtmlTag('title', $attribs, $htmlspecialchars ? oldHtmlspecialchars(strip_tags($title)) : strip_tags($title), true) . "\n";
	}
}
