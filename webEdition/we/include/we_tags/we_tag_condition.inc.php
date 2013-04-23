<?php

/**
 * webEdition CMS
 *
 * $Rev: 4188 $
 * $Author: mokraemer $
 * $Date: 2012-03-04 14:48:38 +0100 (Sun, 04 Mar 2012) $
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
function we_parse_tag_condition($attribs, $content){
	eval('$attribs = ' . $attribs . ';');
	$attribs['_type'] = 'start';
	return '<?php ' . we_tag_tagParser::printTag('condition', $attribs) . ';?>' . $content . '<?php ' . we_tag_tagParser::printTag('condition', array('_type' => 'stop')) . ';?>';
}

function we_tag_condition($attribs){
	$name = weTag_getAttribute('name', $attribs, 'we_lv_condition');
	//internal Attribute
	$_type = weTag_getAttribute('_type', $attribs);
	switch($_type){
		case 'start':

			$GLOBALS['we_lv_conditionCount'] = isset($GLOBALS['we_lv_conditionCount']) ? intval($GLOBALS['we_lv_conditionCount']) + 1 : 1;

			if($GLOBALS['we_lv_conditionCount'] == 1){
				$GLOBALS['we_lv_conditionName'] = $name;
				$GLOBALS[$GLOBALS['we_lv_conditionName']] = '(';
			} else{
				$GLOBALS[$GLOBALS['we_lv_conditionName']] .= '(';
			}
			break;
		case 'stop':
			$GLOBALS[$GLOBALS['we_lv_conditionName']] .= ')';
			$GLOBALS['we_lv_conditionCount']--;
			break;
	}
	return '';
}