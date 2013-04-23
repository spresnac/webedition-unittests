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



$createUser = we_button::create_button("create_user", "javascript:top.opener.top.we_cmd('new_user');", true, -1, -1, "", "", !we_hasPerm("NEW_USER"));
$createAlias = $createGroup = "";

$createGroup = we_button::create_button("create_group", "javascript:top.opener.top.we_cmd('new_group');", true, -1, -1, "", "", !we_hasPerm("NEW_GROUP"));
$createAlias = we_button::create_button("create_alias", "javascript:top.opener.top.we_cmd('new_alias');", true, -1, -1, "", "", !we_hasPerm("NEW_ALIAS"));

$content = $createUser.we_html_tools::getPixel(2,14).$createGroup.we_html_tools::getPixel(2,14).$createAlias;

$modimage = "user.gif";