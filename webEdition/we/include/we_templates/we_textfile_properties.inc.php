<?php

/**
 * webEdition CMS
 *
 * $Rev: 3953 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 19:12:45 +0100 (Tue, 07 Feb 2012) $
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
$parts = array();
array_push($parts, array("icon" => "path.gif", "headline" => g_l('weClass', "[path]"), "html" => $GLOBALS['we_doc']->formPath(), "space" => 120));
array_push($parts, array("icon" => "charset.gif", "headline" => g_l('weClass', "[Charset]"), "html" => $GLOBALS['we_doc']->formCharset(), "space" => 120));
array_push($parts, array("icon" => "user.gif", "headline" => g_l('weClass', "[owners]"), "html" => $GLOBALS['we_doc']->formCreatorOwners(), "space" => 120));
array_push($parts, array("icon" => "copy.gif", "headline" => g_l('weClass', "[copy" . $GLOBALS['we_doc']->ContentType . ']'), "html" => $GLOBALS['we_doc']->formCopyDocument(), "space" => 120));

print we_multiIconBox::getHTML("", "100%", $parts, 30);
