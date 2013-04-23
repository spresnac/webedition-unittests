<?php
/**
 * webEdition CMS
 *
 * $Rev: 5090 $
 * $Author: mokraemer $
 * $Date: 2012-11-07 10:16:55 +0100 (Wed, 07 Nov 2012) $
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
include_once(WE_INCLUDES_PATH . "java_menu/modules/module_menu_messaging.inc.php");
include_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );

we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET;

$jmenu = new weJavaMenu($we_menu_messaging, 'top.opener.top.load', 300);
echo we_html_element::jsScript(JS_DIR . 'images.js');
?>
<script type="text/javascript"><!--
	function menuaction(cmd){
		top.opener.top.load.location.replace("<?php echo WEBEDITION_DIR; ?>we_lcmd.php?we_cmd[0]="+cmd);
	}
	//-->
</script>

<body style="background-color:#efefef;background-image: url(<?php print IMAGE_DIR ?>java_menu/background.gif); background-repeat:repeat;margin:0px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align=left valign=top>
				<?php $jmenu->printMenu('messaging_cmd'); ?>
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
