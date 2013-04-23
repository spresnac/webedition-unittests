<?php

/**
 * webEdition CMS
 *
 * $Rev: 4023 $
 * $Author: mokraemer $
 * $Date: 2012-02-14 20:18:19 +0100 (Tue, 14 Feb 2012) $
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
$znr = -1;
array_push($parts, array("icon" => "path.gif", "headline" => g_l('weClass', "[path]"), "html" => $GLOBALS['we_doc']->formPath(), "space" => 140));
array_push($parts, array("icon" => "doc.gif", "headline" => g_l('weClass', "[document]"), "html" => $GLOBALS['we_doc']->formDocTypeTempl(), "space" => 140));
array_push($parts, array("icon" => "meta.gif", "headline" => g_l('weClass', "[metainfo]"), "html" => $GLOBALS['we_doc']->formMetaInfos(), "space" => 140));
array_push($parts, array("icon" => "cat.gif", "headline" => g_l('global', "[categorys]"), "html" => $GLOBALS['we_doc']->formCategory(), "space" => 140));
array_push($parts, array("icon" => "navi.gif", "headline" => g_l('global', "[navigation]"), "html" => $GLOBALS['we_doc']->formNavigation(), "space" => 140));
array_push($parts, array("icon" => "copy.gif", "headline" => g_l('weClass', "[copyWeDoc]"), "html" => $GLOBALS['we_doc']->formCopyDocument(), "space" => 140));

$wepos = weGetCookieVariable("but_weDocProp");


array_push($parts, array("icon" => "user.gif", "headline" => g_l('weClass', "[owners]"), "html" => $GLOBALS['we_doc']->formCreatorOwners(), "space" => 140));
$znr = 5;


print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("weDocProp", "100%", $parts, 20, "", -1, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"));