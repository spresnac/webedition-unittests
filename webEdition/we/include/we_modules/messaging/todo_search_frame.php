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

if(!preg_match('|^([a-f0-9]){32}|i', $_REQUEST['we_transaction'])){
	exit();
}

print STYLESHEET;
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--
	function doSearch() {
		top.content.messaging_cmd.location = '<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?mcmd=search_messages&we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&searchterm=' + document.we_messaging_search.messaging_search_keyword.value;
	}

	function launchAdvanced() {
		new jsWindow("<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_search_advanced.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>","messaging_search_advanced",-1,-1,300,240,true,false,true,false);
	}

	function clearSearch() {
		document.we_messaging_search.messaging_search_keyword.value = "";
		doSearch();
	}
	//-->
</script>
</head>
<body marginwidth="10" marginheight="7" topmargin="7" leftmargin="7" background="<?php echo IMAGE_DIR; ?>msg_white_bg.gif">
<nobr>
	<form name="we_messaging_search" action="<?php print WE_MESSAGING_MODULE_DIR ?>todo_search_frame.php" onSubmit="return doSearch()">
		<?php echo we_html_tools::hidden('we_transaction', $_REQUEST['we_transaction']) ?>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="defaultfont"><?php print g_l('modules_messaging', "[search_todos]"); ?>:</td>
				<?php
				echo '<td class="defaultfont">' . we_html_tools::getPixel(10, 1) . we_html_tools::htmlTextInput('messaging_search_keyword', 15, isset($_REQUEST['messaging_search_keyword']) ? $_REQUEST['messaging_search_keyword'] : '', 15) . '</td>';
				echo '<td class="defaultfont">' . we_html_tools::getPixel(10, 1) . '</td>';
				print "<td>" . we_button::create_button_table(array(we_button::create_button("search", "javascript:doSearch();"),
						we_button::create_button("advanced", "javascript:launchAdvanced()"),
						we_button::create_button("reset_search", "javascript:clearSearch()")), 10)
					. "</td>";
				?>
			</tr>
		</table>
	</form>
</nobr>
</body>
</html>
