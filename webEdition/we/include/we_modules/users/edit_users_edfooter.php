<?php
/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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

print STYLESHEET;


if(isset($_SESSION["user_session_data"])){
	$user_object = new we_user();
	$user_object->setState($_SESSION["user_session_data"]);
}
?>
</head>
<?php if(isset($user_object)){ ?>
	<body bgcolor="white" background="<?php echo IMAGE_DIR; ?>edit/editfooterback.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
		<table border="0" cellpadding="0" cellspacing="0" width="3000">
			<tr>
				<td></td>
				<td valign="top"><?php we_html_tools::pPixel(1600, 10); ?></td>
			</tr>
			<tr>
				<td width="16"></td>
				<td><?php print we_button::create_button("save", "javascript:top.content.we_cmd('save_user');"); ?></td>
			</tr>
		</table>

	</body>
<?php } ?>
</html>

