<?php
/**
 * webEdition CMS
 *
 * $Rev: 3953 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 19:12:45 +0100 (Tue, 07 Feb 2012) $
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
we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET;

$content = "<p class=\"defaultfont\">" . (isset($we_message) ? $we_message : sprintf(g_l('alert', "[no_perms]"), f("SELECT Username FROM " . USER_TABLE . " WHERE ID='" . $we_doc->CreatorID . "'", "Username", $DB_WE))) . "</p>";
?>
<script  type="text/javascript">
	top.toggleBusy(0);
	var _EditorFrame = top.weEditorFrameController.getEditorFrame(window.name);
	_EditorFrame.setEditorIsLoading(false);
</script>
</head>

<body class="weDialogBody">
<?php
print we_html_tools::htmlDialogLayout($content, g_l('alert', "[no_perms_title]"));
?>
</body>
</html>
