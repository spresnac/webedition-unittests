<?php

/**
 * webEdition CMS
 *
 * $Rev: 5892 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 22:28:04 +0100 (Mon, 25 Feb 2013) $
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
function we_tag_pageLanguage($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs, 'self');
	$type = weTag_getAttribute('type', $attribs);
	$case = weTag_getAttribute('case', $attribs);
	$doc = we_getDocForTag($docAttr);

	$lang = explode('_', $doc->Language);

	switch($type){
		case 'language':
			$out = $lang[0];
			break;
		case 'country':
			$out = $lang[1];
			break;
		case 'language_name':
			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}
			$out = Zend_Locale::getTranslation($lang[0], 'language', $lang[0]);
			break;
		case 'country_name':
			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}
			$out = Zend_Locale::getTranslation($lang[1], 'country', $lang[1]);
			break;
		default:
			$out = $doc->Language;
	}

	switch($case){
		case 'uppercase':
			return strtoupper($out);
		case 'lowercase':
			return strtolower($out);
		default:
			return $out;
	}
}