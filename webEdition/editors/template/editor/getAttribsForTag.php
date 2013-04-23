<?php

/**
 * webEdition CMS
 *
 * $Rev: 4040 $
 * $Author: mokraemer $
 * $Date: 2012-02-15 19:24:09 +0100 (Wed, 15 Feb 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$tagName = isset($_REQUEST['tagName']) ? $_REQUEST['tagName'] : "";

// Remove . / \ because of security reasons
$tagName = str_replace('.', '', $tagName);
$tagName = str_replace('/', '', $tagName);
$tagName = str_replace('\\', '', $tagName);

$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
$xml .= "<attributes>\n";

if($tagName){

	$tagData = weTagData::getTagData($tagName);
	foreach($tagData->getAllAttributes() as $attr){
		$xml .= "\t" . '<attribute name="' . $attr . '" />' . "\n";
	}
}

$xml .= "</attributes>\n";

header('Content-Type: text/xml');
print $xml;