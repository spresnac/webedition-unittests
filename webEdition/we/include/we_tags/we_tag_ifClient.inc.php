<?php

/**
 * webEdition CMS
 *
 * $Rev: 4188 $
 * $Author: mokraemer $
 * $Date: 2012-03-04 14:48:38 +0100 (Sun, 04 Mar 2012) $
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
function we_tag_ifClient($attribs){
	$version = weTag_getAttribute('version', $attribs);
	$browser = weTag_getAttribute('browser', $attribs);
	$system = weTag_getAttribute('system', $attribs);

	if($version){
		if(!(preg_match('|up[0-9\.]+|', $version) || preg_match('|down[0-9\.]+|', $version) || preg_match('|eq[0-9\.]+|', $version))){
			exit(parseError(g_l('parser', '[client_version]')));
		}
	}

	$br = we_base_browserDetect::inst();
	if($browser){
		$bro = explode(',', $browser);
		$_browserOfClient = $br->getBrowser();
		$foo_br = in_array($_browserOfClient, $bro);
		// for backwards compatibility
		if(!$foo_br && $_browserOfClient == 'firefox' && in_array('mozilla', $bro)){
			$foo_br = true;
		} else
		if(!$foo_br && $_browserOfClient == 'appleWebKit' && in_array('safari', $bro)){
			$foo_br = true;
		}
	} else{
		$foo_br = true;
	}

	$brv = $br->getBrowserVersion();
	$foo_v = true;
	$ver = str_replace('up', '>=', $version);
	$ver = str_replace('down', '<', $ver);
	$ver = str_replace('eq', '==', $ver);

	if(strpos($ver, '==') !== false){
		eval('$foo_v = (floor($brv)' . $ver . ');');
	} else{
		eval('$foo_v = ($brv' . $ver . ');');
	}
	if($system){
		$sys = explode(',', $system);
		$foo_sys = in_array($br->getSystem(), $sys);
	} else{
		$foo_sys = true;
	}
	return $foo_br && $foo_v && $foo_sys;
}
