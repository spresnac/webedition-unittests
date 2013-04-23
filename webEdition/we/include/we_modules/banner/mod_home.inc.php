<?php
/**
 * webEdition CMS
 *
 * $Rev: 3464 $
 * $Author: mokraemer $
 * $Date: 2011-11-20 19:01:05 +0100 (Sun, 20 Nov 2011) $
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



$createBanner = we_button::create_button("new_banner", "javascript:top.opener.top.we_cmd('new_banner');", true, -1, -1, "", "", !we_hasPerm("NEW_BANNER"));
$createGroup = we_button::create_button("new_bannergroup", "javascript:top.opener.top.we_cmd('new_bannergroup');", true, -1, -1, "", "", !we_hasPerm("NEW_BANNER"));


$content = $createBanner.we_html_tools::getPixel(2,14).$createGroup;

$modimage = "banner.gif";
