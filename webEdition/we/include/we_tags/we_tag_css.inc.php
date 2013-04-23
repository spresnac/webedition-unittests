<?php

/**
 * webEdition CMS
 *
 * $Rev: 5908 $
 * $Author: lukasimhof $
 * $Date: 2013-03-01 21:25:12 +0100 (Fri, 01 Mar 2013) $
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
function we_tag_css($attribs){

	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}
	$id = weTag_getAttribute('id', $attribs);
	$rel = weTag_getAttribute('rel', $attribs, 'stylesheet');

	$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $GLOBALS['DB_WE']);
	if(!empty($row)){
		$url = $row['Path'] . ($row['IsFolder'] ? '/' : '');

		//	remove not needed elements
		$attribs = removeAttribs($attribs, array('id', 'rel'));

		$attribs['rel'] = $rel;
		$attribs['type'] = 'text/css';
		$attribs['href'] = (we_isHttps() ? '' : BASE_CSS) . $url;

		$nolink = false;
		$applyto = weTag_getAttribute("applyto", $attribs, defined("CSSAPPLYTO_DEFAULT") ? CSSAPPLYTO_DEFAULT : "around");
		switch($applyto){
			case 'around' :
				break;
			case 'wysiwyg' : 
				$nolink = true;
			case 'all' : 
				$media = weTag_getAttribute('media', $attribs);
				if($media == "" || $media == "screen" || $media == "all"){
					$GLOBALS['we_doc']->addDocumentCss($attribs['href'] . "?" . time());
				}
				break;
		}
		return $nolink ? '' : getHtmlTag('link', $attribs) . "\n";
	}
	return '';
}