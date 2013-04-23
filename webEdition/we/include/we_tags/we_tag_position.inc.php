<?php

/**
 * webEdition CMS
 *
 * $Rev: 5653 $
 * $Author: arminschulz $
 * $Date: 2013-01-28 05:39:03 +0100 (Mon, 28 Jan 2013) $
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
function we_tag_position($attribs){
	global $lv;

	//	type is required !!!
	if(($missingAttrib = attributFehltError($attribs, "type", __FUNCTION__))){
		print $missingAttrib;
		return "";
	}

	//	here we get the needed attributes
	$type = weTag_getAttribute("type", $attribs);
	$_reference = weTag_getAttribute("reference", $attribs);
	$format = weTag_getAttribute("format", $attribs, 1);
	//	this value we will return later
	$_retPos = "";

	switch($type){

		case "listview" : //	inside a listview, we take direct global listview object
			$_retPos = ($lv->start + $lv->count);
			break;

		case "listdir" : //	inside a listview
			if(isset($GLOBALS['we_position']['listdir'])){
				$_content = $GLOBALS['we_position']['listdir'];
			}
			if(isset($_content) && $_content['position']){
				$_retPos = $_content['position'];
			}
			break;

		case "linklist" : //	look in fkt we_tag_linklist and class we_linklist for details
			$missingAttrib = attributFehltError($attribs, "reference", __FUNCTION__);// seperate because of #6890
			if($missingAttrib){
				print $missingAttrib;
				return "";
			}
			foreach($GLOBALS['we_position'][$type] as $name => $arr){

				if(strpos($name, $_reference) === 0){
					if(is_array($arr)){
						$_content = $arr;
					}
				}
			}
			if(isset($_content) && isset($_content['position'])){
				$_retPos = $_content['position'];  //  #6890
			}
			break;
		
		case "block" : //	look in function we_tag_block for details
			//	first we must get right array !!!
			$missingAttrib = attributFehltError($attribs, "reference", __FUNCTION__);
			if($missingAttrib){
				print $missingAttrib;
				return "";
			}
			foreach($GLOBALS['we_position'][$type] as $name => $arr){

				if(strpos($name, $_reference) === 0){
					if(is_array($arr)){
						$_content = $arr;
					}
				}
			}
			if(isset($_content) && $_content['position']){
				$_retPos = $_content['position'];
			}
			break;
	}

	//	convert to desired format
	switch($format){

		case "a" :
			return number2System($_retPos);
			break;

		case "A" :
			return strtoupper(number2System($_retPos));
			break;

		default :
			return $_retPos;
			break;
	}
}
