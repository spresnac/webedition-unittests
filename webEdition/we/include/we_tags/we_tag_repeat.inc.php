<?php

/**
 * webEdition CMS
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
function we_parse_tag_repeat($attribs, $content){
	return '<?php while(' . we_tag_tagParser::printTag('repeat', $attribs) . '){?>' . $content . '<?php }?>';
}

function we_tag_repeat(){
	if(isset($GLOBALS["_we_voting_list"])){
		return $GLOBALS["_we_voting_list"]->getNext();
	} else{
		if(isset($GLOBALS["lv"])){
			if($GLOBALS["lv"]->next_record()){
				$GLOBALS["we_lv_array"][(count($GLOBALS["we_lv_array"]) - 1)] = clone($GLOBALS["lv"]);
				if($GLOBALS["lv"]->ClassName == 'we_listview_object'){
					$GLOBALS['_we_listview_object_flag'] = true;
				}
				return true;
			} else{ //last entry
				unset($GLOBALS['_we_listview_object_flag']);
				return false;
			}
		}
	}
	return false;
}