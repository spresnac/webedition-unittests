<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
 * @return string
 * @param array $attribs
 * @param string $content
 */
function we_tag_xmlfeed($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($attribs, 'url', __FUNCTION__)))
		return $foo;

	$name = weTag_getAttribute('name', $attribs);
	$url = weTag_getAttribute('url', $attribs);
	$refresh = abs(weTag_getAttribute('refresh', $attribs, 30)) * 60;
	$timeout = abs(weTag_getAttribute('timeout', $attribs, 0));

	if(!isset($GLOBALS['xmlfeeds'])){
		$GLOBALS['xmlfeeds'] = array();
	}

	$GLOBALS['xmlfeeds'][$name] = new weXMLBrowser();
	$cache = $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'xmlfeeds/' . $name;

	$do_refresh = (is_file($cache) && $refresh > 0 ? ((filemtime($cache) + $refresh) < time()) : true);

	if(!is_file($cache) || $do_refresh){
		$ret = $GLOBALS['xmlfeeds'][$name]->getFile($url, $timeout);
		if($ret){
			$GLOBALS['xmlfeeds'][$name]->saveCache($cache, time() + (2 * $refresh)); //keep file longer, in case of timeouts
		} else if($timeout && is_file($cache)){
			//timeout + last file exists
			$GLOBALS['xmlfeeds'][$name]->loadCache($cache);
		}
	} else{
		$GLOBALS['xmlfeeds'][$name]->loadCache($cache);
	}
}
