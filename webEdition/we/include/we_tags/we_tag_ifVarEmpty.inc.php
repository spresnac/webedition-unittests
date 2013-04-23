<?php

/**
 * webEdition CMS
 *
 * $Rev: 5136 $
 * $Author: mokraemer $
 * $Date: 2012-11-13 00:10:47 +0100 (Tue, 13 Nov 2012) $
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
include_once (WE_INCLUDES_PATH . 'we_tags/we_tag_ifVarSet.inc.php');

function we_isVarNotEmpty($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs);
	$match_orig = weTag_getAttribute('match', $attribs);
	$match = we_tag_getPostName($match_orig); //#6367

	$name = weTag_getAttribute('name', $attribs);

	$type = weTag_getAttribute('type', $attribs, 'txt');
	$formname = weTag_getAttribute('formname', $attribs, 'we_global_form');
	$property = weTag_getAttribute('property', $attribs, false, true);

	if(!we_isVarSet($match, $match_orig, $type, $docAttr, $property, $formname)){
		return false;
	}

	switch($type){
		case 'request' :
			return (strlen($_REQUEST[$match_orig]) > 0);
		case 'post' :
			return (strlen($_POST[$match_orig]) > 0);
		case 'get' :
			return (strlen($_GET[$match_orig]) > 0);
		case 'global' :
			return (strlen($GLOBALS[$match_orig]) > 0);
		case 'session' :
			$foo = isset($_SESSION[$match_orig]) ? $_SESSION[$match_orig] : '';
			return (strlen($foo) > 0);
		case 'sessionfield' :
			return (strlen($_SESSION['webuser'][$match_orig]) > 0);
		default :
			$doc = false;
			switch($docAttr){
				case 'object' :
				case 'document' :
					$doc = isset($GLOBALS['we_' . $docAttr][$formname]) ? $GLOBALS['we_' . $docAttr][$formname] : false;
					break;
				case 'top' :
					$doc = isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC'] : false;
					break;
				default :
					$doc = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : false;
			}
			if($doc){
				if($property){
					return isset($doc->$match_orig) ? $doc->$match_orig : '';
				} else{
					$name = $match;
					switch($type){
						case 'href' :
							$attribs['name'] = $match;
							$attribs['_name_orig'] = $match_orig;
							$foo = $doc->getField($attribs, $type, true);
							break;
						case 'multiobject' :
							//FIXME: this makes no sense
							$attribs['name'] = $match;
							$attribs['_name_orig'] = $match_orig;
							$data = unserialize($doc->getField($attribs, $type, true));
							if(!is_array($data['objects'])){
								$data['objects'] = array();
							}
							$temp = new we_listview_multiobject($match);
							return (!empty($temp->Record));
						default :
							$foo = $doc->getElement($match);
							if(!$foo){
								$foo = $doc->getElement($match_orig);
							}
					}
					return (strlen($foo) > 0);
				}
			}
			return false;
	}
}

function we_tag_ifVarEmpty($attribs){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		print($foo);
		return false;
	}
	return !we_isVarNotEmpty($attribs);
}
