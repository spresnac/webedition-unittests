<?php

/**
 * webEdition CMS
 *
 * $Rev: 3382 $
 * $Author: mokraemer $
 * $Date: 2011-10-24 01:33:15 +0200 (Mon, 24 Oct 2011) $
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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


/**
 * Language file: buttons.inc.php
 *
 * Provides language strings.
 *
 * Language: Deutsch
 */

$l_button=array();
$dir=dirname(__FILE__).'/buttons/';
include($dir."global.inc.php");
$l_button=array_merge($l_button,$l_buttons_global);
unset($l_buttons_global);
if (is_dir($dir."modules")) {

	// Include language files of buttons used in modules
	$d = dir($dir."modules");
	while (false !== ($entry = $d->read())) {
		$var=substr($entry,0,-8);
		if (substr($entry,-8 ) == '.inc.php') {
			include($dir."modules/".$entry);
			$l_button=array_merge($l_button,${"l_buttons_modules_$var"});
			unset(${"l_buttons_modules_$var"});
		}
	}
	$d->close();
}
unset($dir);
