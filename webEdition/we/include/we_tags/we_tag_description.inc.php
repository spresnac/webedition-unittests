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
/*function we_parse_tag_description($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('description', $attribs, $content, true) . ');?>';
}*/

function we_tag_description($attribs, $content){
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
	$max = weTag_getAttribute('max', $attribs, 0);
	$attribs = removeAttribs($attribs, array(
		'htmlspecialchars','max'
		));

	if($GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS['we_doc']->InWebEdition){ //	normally meta tags are edited on property page
		return '<?php	$GLOBALS["meta"]["Description"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	} else{

		$descr = isset($GLOBALS['DESCRIPTION']) && $GLOBALS['DESCRIPTION'] ? $GLOBALS['DESCRIPTION'] : $content;

		$attribs["name"] = "description";
		$descr = $htmlspecialchars ? oldHtmlspecialchars(strip_tags($descr)) : strip_tags($descr);
		$attribs["content"] = $max?cutText($descr,$max):$descr;
		return getHtmlTag("meta", $attribs) . "\n";
	}
}
