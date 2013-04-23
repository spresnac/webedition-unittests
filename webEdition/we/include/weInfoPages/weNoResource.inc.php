<?php
/**
 * webEdition CMS
 *
 * $Rev: 4935 $
 * $Author: mokraemer $
 * $Date: 2012-09-03 20:58:22 +0200 (Mon, 03 Sep 2012) $
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

print STYLESHEET .
	we_html_element::jsElement('
	top.toggleBusy(0);
	var _EditorFrame = top.weEditorFrameController.getEditorFrame(window.name);
	_EditorFrame.setEditorIsLoading(false);');
?>
</head>

<body class="weDialogBody">
	<?php
	print we_html_tools::htmlDialogLayout('<p class="defaultfont">' . g_l('alert', "[noResource]") . '</p>', g_l('alert', '[noResourceTitle]'));
	?>
</body>
</html>
