<?php

/**
 * webEdition CMS
 *
 * $Rev: 5248 $
 * $Author: arminschulz $
 * $Date: 2012-11-28 05:24:00 +0100 (Wed, 28 Nov 2012) $
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

/**
 *
 * @param mixed $_position position-value of position-Array (first,last,even,odd,#)
 * @param int $_size size of position Array
 * @param string $operator operator (equal,less,greater,less|equal,greater|equal)
 * @param int $position position of comparable
 * @param int $size size of comparable
 * @return mixed (true,false,-1) -1 if no decission is made yet - pass next element of position array
 */
function _we_tag_ifPosition_op($_position, $_size, $operator, $position, $size){
	switch($_position){
		case "first" :
			if($_size == 1 && $operator != ''){
				switch($operator){
					case "equal": return $position == 1;
					case "less": return $position < 1;
					case "less|equal": return $position <= 1;
					case "greater": return $position > 1;
					case "greater|equal": return $position >= 1;
				}
			} else{
				if($position == 1){
					return true;
				}
			}
			break;
		case "last" :
			if($_size == 1 && $operator != ''){
				switch($operator){
					case "equal": return $position == $size;
					case "less": return $position < $size;
					case "less|equal": return $position <= $size;
					case "greater|equal": return $position >= $size;
				}
			} else{
				if($position == $size){
					return true;
				}
			}
			break;
		case "odd" :
			if($position % 2 != 0){
				return true;
			}
			break;
		case "even" :
			if($position % 2 == 0){
				return true;
			}
			break;

		default :
			$_position = intval($_position); // Umwandeln in integer
			if($_size == 1 && $operator != ''){
				switch($operator){
					case "equal": return $position == $_position;
					case "less": return $position < $_position;
					case "less|equal": return $position <= $_position;
					case "greater": return $position > $_position;
					case "greater|equal": return $position >= $_position;
					case "every": return ($position % $_position == 0);
				}
			} else{
				if($operator == 'every' && ($position % $_position == 0)){
					return true;
				} else if($position == $_position){
					return true;
				}
			}
			break;
	}
	//no decission yet
	return -1;
}

function we_tag_ifPosition($attribs){
	//	content is not needed in this tag
	//Hack for linklist
	if(isset($GLOBALS['we']['ll'])){
		$attribs['type'] = 'linklist';
	}
	if(($missingAttrib = attributFehltError($attribs, "type", __FUNCTION__) || attributFehltError($attribs, "position", __FUNCTION__))){
		print $missingAttrib;
		return '';
	}


	$type = weTag_getAttribute("type", $attribs);
	$position = weTag_getAttribute("position", $attribs);
	$positionArray = explode(',', $position);
	$_size = count($positionArray);
	$operator = weTag_getAttribute("operator", $attribs);

	switch($type){
		case "listview" : //	inside a listview, we take direct global listview object
			foreach($positionArray as $_position){
				$tmp = _we_tag_ifPosition_op($_position, $_size, $operator, $GLOBALS['lv']->count, $GLOBALS['lv']->anz);
				if($tmp !== -1){
					return $tmp;
				}
			}
			break;

		case "linklist" :
			//	first we must get right array !!!
			$_reference = $GLOBALS['we']['ll']->getName();

			$_reference = $GLOBALS['we_position']['linklist'][$_reference];

			if(is_array($_reference) && isset($_reference['position'])){
				foreach($positionArray as $_position){
					$tmp = _we_tag_ifPosition_op($_position, $_size, $operator, $_reference['position']+1, $_reference['size']);
					if($tmp !== -1){
						return $tmp;
					}
				}
			}

			break;

		case "block" : //	look in function we_tag_block for details
			$_reference=substr($GLOBALS['postTagName'],4,strrpos($GLOBALS['postTagName'],'__')-4);//strip leading blk_ and trailing __NO
			$_reference = $GLOBALS['we_position']['block'][$_reference];

			if(is_array($_reference) && isset($_reference['position'])){
				foreach($positionArray as $_position){
					$tmp = _we_tag_ifPosition_op($_position, $_size, $operator, $_reference['position'], $_reference['size']);
					if($tmp !== -1){
						return $tmp;
					}
				}
			}
			break;

		case "listdir" : //	inside a listview
			if(isset($GLOBALS['we_position']['listdir'])){
				$_content = $GLOBALS['we_position']['listdir'];
			}
			if(isset($_content) && $_content['position']){
				foreach($positionArray as $_position){
					$tmp = _we_tag_ifPosition_op($_position, $_size, $operator, $_content['position'], $_content['size']);
					if($tmp !== -1){
						return $tmp;
					}
				}
			}
			break;
		default :
			return false;
	}
	return false;
}
