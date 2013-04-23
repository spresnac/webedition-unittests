<?php

/**
 * webEdition CMS
 *
 * $Rev: 5779 $
 * $Author: mokraemer $
 * $Date: 2013-02-09 20:34:19 +0100 (Sat, 09 Feb 2013) $
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
function we_tag_sidebar($attribs, $content){
	if(SIDEBAR_DISABLED == 0 && we_tag('ifNotSidebar', $attribs, $content) && we_tag('ifEditmode', $attribs, $content)){
		$id = weTag_getAttribute('id', $attribs, 0);
		$file = weTag_getAttribute('file', $attribs);
		$url = weTag_getAttribute('url', $attribs);
		$anchor = weTag_getAttribute('anchor', $attribs);
		$width = weTag_getAttribute('width', $attribs, (defined('WE_SIDEBAR_WIDTH') ? WE_SIDEBAR_WIDTH : 300));
		$params = weTag_getAttribute('params', $attribs);
		if($params && strpos($params, '?') === 0){
			$params = substr($params, 1);
		}

		removeAttribs($attribs, array('id', 'file', 'url', 'width', 'href', 'params'));

		if(trim($content) == ''){
			$content = g_l('tags', '[open_sidebar]');
		}

		$href = '#';
		if($id == 0){
			if($file != ''){
				$href = "javascript:top.weSidebar.load('" . $file . "');top.weSidebar.resize(" . $width . ",'" . $params . "');";
			} else if($url != ""){
				$href = "javascript:top.weSidebar.load('" . $url . "');top.weSidebar.resize(" . $width . ",'" . $params . "');";
			} else{
				return;
			}
		} else{
			$href = "javascript:top.weSidebar.open('" . $id . "', " . $width . ",'" . $params . "');";
		}
		$attribs['href'] = $href;

		return getHtmlTag('a', $attribs, $content);
	}
	return '';
}