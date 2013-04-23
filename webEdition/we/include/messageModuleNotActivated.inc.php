<?php
/**
 * webEdition CMS
 *
 * $Rev: 4082 $
 * $Author: mokraemer $
 * $Date: 2012-02-19 02:03:52 +0100 (Sun, 19 Feb 2012) $
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
we_html_tools::htmlTop();
print STYLESHEET;

$content = '
<table border="0" cellpadding="7" width="100%" class="defaultfont">
<tr>
	<td colspan="2"><strong>' . sprintf(
		g_l('moduleActivation', '[headline]'), $_moduleName) . '</strong></td>
</tr>
<tr>
	<td valign="top">
		<img src="' . IMAGE_DIR . "alert.gif" . '" />
	</td>
	<td class="defaultfont">
		' . g_l('moduleActivation', '[content]') . '
	</td>
</tr>
</table>';
?>
</head>

<body bgcolor="#ffffff"
			background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif"
			onload="self.focus();" onBlur="setTimeout('self.close()',500);">
				<?php print $content ?>
</body>
</html>