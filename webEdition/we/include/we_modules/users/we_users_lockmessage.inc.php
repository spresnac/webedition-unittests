<?php
/**
 * webEdition CMS
 *
 * $Rev: 5060 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 16:57:00 +0100 (Sun, 04 Nov 2012) $
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

$foo = f("SELECT username FROM " . USER_TABLE . " WHERE ID=$_userID", "username", $GLOBALS['DB_WE']);


$content = "<p class='defaultfont'>" . sprintf(g_l('alert', "[temporaere_no_access_text]"), $we_doc->Text, $foo) . "</p>";
?>
<script  type="text/javascript">
	<!--
	top.toggleBusy(0);
	//-->
</script>
</head>

<body class="weDialogBody">
<?php
print we_html_tools::htmlDialogLayout($content, g_l('alert', "[temporaere_no_access]"));

//	For SEEM-Mode
if($_SESSION['weS']['we_mode'] == "seem"){
	?><a href="javascript://" style="text-decoration:none" onClick="top.weNavigationHistory.navigateReload()" ><?php print g_l('SEEM', "[try_doc_again]") ?></a>
		<?php
	}
	?>
</body>
</html>
