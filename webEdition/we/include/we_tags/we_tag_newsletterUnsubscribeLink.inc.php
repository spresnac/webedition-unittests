<?php

/**
 * webEdition CMS
 *
 * $Rev: 5576 $
 * $Author: mokraemer $
 * $Date: 2013-01-16 21:56:32 +0100 (Wed, 16 Jan 2013) $
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
function we_tag_newsletterUnsubscribeLink($attribs){
	$foo = attributFehltError($attribs, "id", __FUNCTION__);
	if($foo)
		return $foo;
	$id = weTag_getAttribute("id", $attribs);
	$plain = weTag_getAttribute("plain", $attribs, true, true);

	$db = $GLOBALS['DB_WE'];
	$settings = array();
	$db->query('SELECT * FROM ' . NEWSLETTER_PREFS_TABLE . ' WHERE pref_name IN ("use_port","use_https_refer")');

	while($db->next_record()) {
		$settings[$db->f("pref_name")] = $db->f("pref_value");
	}

	$port = (isset($settings["use_port"]) && $settings["use_port"]) ? ":" . $settings["use_port"] : '';
	$protocol = (isset($settings["use_https_refer"]) && $settings["use_https_refer"]) ? 'https://' : 'http://';

	$ret = getServerUrl() . id_to_path($id, FILE_TABLE) . '?we_unsubscribe_email__=###EMAIL###';
	return ($plain ? $ret : '<a href="' . $ret . '">' . $ret . '</a>');
}
