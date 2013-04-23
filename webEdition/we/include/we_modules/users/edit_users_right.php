<?php
/**
 * webEdition CMS
 *
 * $Rev: 4749 $
 * $Author: mokraemer $
 * $Date: 2012-07-23 00:02:51 +0200 (Mon, 23 Jul 2012) $
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
we_html_tools::protect();

we_html_tools::htmlTop();
?>
</head>

<?php if(we_base_browserDetect::isGecko()){ ?>
	<frameset cols="*" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php print WE_USERS_MODULE_DIR; ?>edit_users_editor.php" scrolling="no" noresize name="user_editor"/>
<?php } else if(we_base_browserDetect::isSafari()){ ?>
	<frameset cols="1,*" framespacing="0" border="0" frameborder="NO">
		<frame src="<?php print HTML_DIR; ?>safariResize.html" name="user_separator" noresize scrolling="no"/>
		<frame src="<?php print WE_USERS_MODULE_DIR; ?>edit_users_editor.php" noresize name="user_editor" scrolling="no"/>
	</frameset>
<?php } else{ ?>
	<frameset cols="2,*" framespacing="0" border="0" frameborder="NO">
		<frame src="<?php print HTML_DIR; ?>ieResize.html" name="user_separator" noresize scrolling="no"/>
		<frame src="<?php print WE_USERS_MODULE_DIR; ?>edit_users_editor.php" noresize name="user_editor" scrolling="no"/>
	</frameset>
<?php } ?>
<noframes>
	<body bgcolor="#ffffff">
		<p></p>
	</body>
</noframes>
</html>

