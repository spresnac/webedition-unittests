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
function we_isNotEmpty($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$match = we_tag_getPostName(weTag_getAttribute('match', $attribs));
	$doc = we_getDocForTag($docAttr, false);

	switch($type){
		case 'object' :
			return (bool) $doc->getElement($match);
		case 'binary' :
		case 'img' :
		case 'flashmovie' :
			return (bool) $doc->getElement($match, 'bdid');
		case 'href' :
			if(isset($doc->TableID) && $doc->TableID){
				$hreftmp = $doc->getElement($match);
				$hreftmp = $hreftmp ? $hreftmp : '';
				if(!is_array($hreftmp)){
					return true;
				}
				$hreftmp = trim(we_document::getHrefByArray($hreftmp));
				if(substr($hreftmp, 0, 1) == '/'){
					return (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp));
				}
				return $hreftmp ? true : false;
			}
			$int = intval($doc->getElement($match . '_we_jkhdsf_int'));
			if($int){ // for type = href int
				$intID = $doc->getElement($match . '_we_jkhdsf_intID');
				return (bool) ($intID > 0) && strlen(id_to_path(array($intID)));
			} else{
				$hreftmp = $doc->getElement($match);
				if(substr($hreftmp, 0, 1) == '/'){
					return (bool) (file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp));
				}
			}
		default :

			if(isset($doc)){
				//   #3938 added this - some php version crashed, when unserialize started with a ?,?,?


				if((substr($doc->getElement($match), 0, 2) == 'a:')){ //  only unserialize, when $match cluld be an array
					// Added @-operator in front of the unserialze function because there
					// were some PHP notices that had no effect on the output of the function
					// remark holeg: when it is a serialized array, the function looks if it is not empty
					if(is_array(
							$arr = @unserialize($doc->getElement($match)))){
						return !empty($arr);
					}
				}
				//   end of #3938
			}
	}
	return (bool) ($doc->getElement($match) != '') || $doc->getElement($match, 'bdid');
}

function we_tag_ifEmpty($attribs, $content){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		print($foo);
		return '';
	}
	if(isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']){
		return true;
	}
	return !we_isNotEmpty($attribs);
}
