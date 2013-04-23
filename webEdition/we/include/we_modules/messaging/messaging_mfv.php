<?php
/**
 * webEdition CMS
 *
 * $Rev: 4085 $
 * $Author: mokraemer $
 * $Date: 2012-02-19 13:00:54 +0100 (Sun, 19 Feb 2012) $
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

we_html_tools::htmlTop();

print STYLESHEET;
?>
</head>
<?php if(we_base_browserDetect::isGecko()){ ?>
	<frameset rows="180,*" framespacing="0" border="1" frameborder="1">
		<frame src="<?php echo HTML_DIR ?>white_inc.html" name="messaging_messages_overview" scrolling="auto"/>
		<frame src="<?php echo HTML_DIR ?>white_inc.html" name="messaging_msg_view"/>
	</frameset>
<?php } else if(we_base_browserDetect::isSafari()){ ?>
	<frameset rows="180,1,*" framespacing="0" border="0" frameborder="0" id="msg_resize_frameset">
		<frame src="<?php echo HTML_DIR ?>white_inc.html" name="messaging_messages_overview" scrolling="auto"/>
		<frame src="safariHResize.html" name="messaging_separator"/>
		<frame src="<?php echo HTML_DIR ?>white_inc.html" name="messaging_msg_view" scrolling="auto"/>
	</frameset>

<?php } else{ ?>
	<frameset rows="180,*" framespacing="0" border="1" frameborder="0">
		<frame src="<?php echo HTML_DIR ?>white_inc.html" name="messaging_messages_overview" scrolling="auto" style="border-bottom:1px solid black"/>
		<frame src="<?php echo HTML_DIR ?>white_inc.html" name="messaging_msg_view"/>
	</frameset>
<?php } ?>
<body>
</body>
</html>
