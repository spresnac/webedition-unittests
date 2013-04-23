<?php
/**
 * webEdition CMS
 *
 * $Rev: 5084 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 20:32:30 +0100 (Tue, 06 Nov 2012) $
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
include_once(WE_INCLUDES_PATH . "java_menu/modules/module_menu_banner.inc.php");
include_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );
we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'images.js') .
	we_html_element::jsElement('
	       function menuaction(cmd){
				top.opener.top.load.location.replace("' . WEBEDITION_DIR . 'we_lcmd.php?wecmd0="+cmd);
	    }

	');

$jmenu = new weJavaMenu($we_menu_banner, "top.opener.top.load", 350, 30);
?>
</head>
<body style="background-color:#efefef;background-image: url(<?php print IMAGE_DIR ?>java_menu/background.gif); background-repeat:repeat;margin:0px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align=left valign=top>
				<?php $jmenu->printMenu('cmd'); ?>
			</td>
			<td align="right">
				<?php
				print createMessageConsole("moduleFrame");
				?>
			</td>
		</tr>
	</table>
</body>
</html>