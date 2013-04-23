<?php

/**
 * webEdition CMS
 *
 * $Rev: 5643 $
 * $Author: mokraemer $
 * $Date: 2013-01-25 15:55:35 +0100 (Fri, 25 Jan 2013) $
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
function we_isVarSet($name, $orig, $type, $docAttr, $property = false, $formname = '', $shopname = ''){
	switch($type){
		case 'request' :
			return isset($_REQUEST[$orig]);
		case 'post' :
			return isset($_POST[$orig]);
		case 'get' :
			return isset($_GET[$orig]);
		case 'global' :
			return isset($GLOBALS[$name]) || isset($GLOBALS[$orig]);
		case 'session' :
			return isset($_SESSION[$orig]);
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$orig]);
		case 'shopField' :
			return (isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname]->hasCartField($orig) : false);
		case 'sum' :
			return (isset($GLOBALS['summe']) && isset($GLOBALS['summe'][$orig]));
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
					return isset($doc->$name) || isset($doc->orig);
				} else{
					if($type == 'href' && isset($doc->elements[$name . '_we_jkhdsf_int']) && $doc->elements[$name . '_we_jkhdsf_int']['dat']){
						return isset($doc->elements[$name . '_we_jkhdsf_intPath']['dat']);
					}
					if(isset($doc->elements[$name])){
						$fieldType = isset($doc->elements[$name]['type']) ? $doc->elements[$name]['type'] : '';
						$issetElemNameDat = isset($doc->elements[$name]['dat']);
						return ($fieldType == 'checkbox_feld' && $issetElemNameDat && $doc->elements[$name]['dat'] == 0 ?
								false :
								$issetElemNameDat);
					}
				}
			}
			return false;
	}
}

function we_tag_ifVarSet($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		print($foo);
		return false;
	}

	$type = weTag_getAttribute("var", $attribs, weTag_getAttribute("type", $attribs));
	$doc = weTag_getAttribute("doc", $attribs);
	$name = weTag_getAttribute("name", $attribs);
	$name_orig = weTag_getAttribute("_name_orig", $attribs);
	$formname = weTag_getAttribute("formname", $attribs, "we_global_form");
	$property = weTag_getAttribute("property", $attribs, false, true);
	$shopname = weTag_getAttribute('shopname', $attribs);

	return we_isVarSet($name, $name_orig, $type, $doc, $property, $formname, $shopname);
}
