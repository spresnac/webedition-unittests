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
function we_parse_tag_back($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('back', $attribs) . ');?>' . $content . '<?php printElement(' . we_tag_tagParser::printTag('back', array('_type' => 'stop')) . ');?>';
}

function we_tag_back($attribs){
	$_type = weTag_getAttribute('_type', $attribs);
	$attribs = removeAttribs($attribs, array('_type'));

	switch($_type){
		default:
			if(isset($GLOBALS["_we_voting_list"])){
				return $GLOBALS["_we_voting_list"]->getBackLink($attribs);
			} else{
				return $GLOBALS["lv"]->getBackLink($attribs);
			}
		case 'stop':
			if(isset($GLOBALS["_we_voting_list"])){
				return ($GLOBALS["_we_voting_list"]->hasPrevPage() ? '</a>' : '');
			} else{
				return ($GLOBALS["lv"]->hasPrevPage() && $GLOBALS["lv"]->close_a() ? '</a>' : '');
			}
	}
}
