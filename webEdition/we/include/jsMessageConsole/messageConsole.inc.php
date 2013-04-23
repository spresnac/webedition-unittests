<?php

/**
 * webEdition CMS
 *
 * $Rev: 4598 $
 * $Author: mokraemer $
 * $Date: 2012-06-17 02:02:35 +0200 (Sun, 17 Jun 2012) $
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

/**
 * creates a new messageConsole
 *
 * @param string $consoleName
 * @return string
 */
function createMessageConsole($consoleName="NoName"){

	return we_html_element::jsScript(JS_DIR . "messageConsoleImages.js") . we_html_element::jsScript(JS_DIR . "messageConsoleView.js") . we_html_element::jsElement("

var _msgNotice  = \"" . g_l('messageConsole', "[iconBar][notice]") . "\";
var _msgWarning = \"" . g_l('messageConsole', "[iconBar][warning]") . "\";
var _msgError   = \"" . g_l('messageConsole', "[iconBar][error]") . "\";


var _console_$consoleName = new messageConsoleView( '$consoleName', window );
_console_$consoleName.register();

onunload=function() {
	_console_$consoleName.unregister();
}
")."
<div style=\"position:relative;float:left;\">
	<table>
	<tr>
		<td valign=\"middle\">
		<span class=\"small\" id=\"messageConsoleMessage$consoleName\" style=\"display: none; background-color: white; border: 1px solid #cdcdcd; padding: 2px 4px 2px 4px; margin: 3px 10px 0 0;\">
			--
		</span>
		</td>
		<td>
			<div onclick=\"_console_$consoleName.openMessageConsole();\" class=\"navigation_normal\" onmouseover=\"this.className='navigation_hover'\" onmouseout=\"this.className='navigation_normal'\"><img id=\"messageConsoleImage$consoleName\" src=\"" . IMAGE_DIR . "messageConsole/notice.gif\" style=\"border: none; padding: 1px;\" /></div>
		</td>
	</tr>
	</table>
</div>
";
}
