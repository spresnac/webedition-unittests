<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>
<tags>
';
$allWeTags = weTagWizard::getExistingWeTags();
foreach($allWeTags as $tag){
	$tagData = weTagData::getTagData($tag);
	echo  "\t". '<tag needsEndtag="'.($tagData->needsEndTag()? "true" : "false").'" name="' . $tagData->getName() . '" />'."\n";
}
echo "</tags>\n";