<?php
/**
 * webEdition CMS
 *
 * $Rev: 3665 $
 * $Author: mokraemer $
 * $Date: 2011-12-27 15:59:37 +0100 (Tue, 27 Dec 2011) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/*
 * This is the template for tab connect. It trys to connect to the server in
 * different ways.
 */

if ($this->State == 'true') {
	$description = g_l('liveUpdate','[state][descriptionTrue]');
} else {
	$description = g_l('liveUpdate','[state][descriptionError]');
}



$content = '
<div class="defaultfont">
	' . $description . '
	<div class="errorDiv">
		<code>' . $this->Message . '</code>
	</div>
</div>
';

print liveUpdateTemplates::getHtml(g_l('liveUpdate','[state][headline]'), $content);