<?php

/**
 * webEdition CMS
 *
 * $Rev: 5649 $
 * $Author: mokraemer $
 * $Date: 2013-01-26 19:35:13 +0100 (Sat, 26 Jan 2013) $
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
function we_tag_var($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$docAttr = weTag_getAttribute('doc', $attribs);
	$name = weTag_getAttribute('name', $attribs);
	$name_orig = weTag_getAttribute('_name_orig', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true); // #3771
	$doc = we_getDocForTag($docAttr, false);

	switch($type){
		case 'session' :
			$return = (isset($_SESSION[$name_orig])) ? $_SESSION[$name_orig] : '';
			return $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
		case 'request' :
			$return = filterXss(we_util::rmPhp(isset($_REQUEST[$name_orig]) ? $_REQUEST[$name_orig] : ''));
			return $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
		case 'post' :
			$return = we_util::rmPhp(isset($_POST[$name_orig]) ? $_POST[$name_orig] : '');
			return $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
		case 'get' :
			$return = we_util::rmPhp(isset($_GET[$name_orig]) ? $_GET[$name_orig] : '');
			return $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
		case 'global' :
			$return = (isset($GLOBALS[$name])) ? $GLOBALS[$name] : ((isset($GLOBALS[$name_orig])) ? $GLOBALS[$name_orig] : '');
			return $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
		case 'multiobject' :
			$data = unserialize($doc->getField($attribs, $type, true));
			return (isset($data['objects']) && !empty($data['objects']) ? implode(',', $data['objects']) : '');

		case 'property' :
			return (isset($GLOBALS['we_obj']) ?
					$GLOBALS['we_obj']->$name_orig :
					$doc->$name_orig);

		case 'shopVat' :
			if(defined('SHOP_TABLE')){
				$vatId = $doc->getElement(WE_SHOP_VAT_FIELD_NAME);
				return weShopVats::getVatRateForSite($vatId);
			}
			return '';
		case 'link' :
			return $doc->getField($attribs, $type, false);
		// bugfix #3634
		default :
			$normVal = $doc->getField($attribs, $type, true);
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Erg?bnis liefert
			// wird in den eingebundenen Objekten ?berpr?ft ob das Feld existiert
			$name = ($type == 'select' && $normVal == '' ? $name_orig : $name);
			$selectKey = weTag_getAttribute('key', $attribs, false, true);
			if($type == 'select' && $selectKey){
				return $htmlspecialchars ? oldHtmlspecialchars($doc->getElement($name)) : $doc->getElement($name);
			}

			if(isset($doc->DefArray) && is_array($doc->DefArray)){
				$keys = array_keys($doc->DefArray);
				foreach($keys as $_glob_key){
					if((substr($_glob_key, 0, 7) == 'object_' && ($rest = substr($_glob_key, 7))) || (substr($_glob_key, 0, 10) == 'we_object_' && ($rest = substr($_glob_key, 7)))){
						$normVal = $doc->getFieldByVal($doc->getElement($name), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $rest);
					}

					if($normVal != ''){
						return $htmlspecialchars ? oldHtmlspecialchars($normVal) : $normVal;
					}
				}
			}
			// EOF bugfix 7557


			return $normVal;
	}
}
