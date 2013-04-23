<?php
/**
 * webEdition CMS
 *
 * $Rev: 4613 $
 * $Author: lukasimhof $
 * $Date: 2012-06-21 12:15:40 +0200 (Thu, 21 Jun 2012) $
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
include_once($_SERVER['DOCUMENT_ROOT'] . WE_MESSAGING_MODULE_DIR . "msg_html_tools.inc.php");
we_html_tools::protect();
we_html_tools::htmlTop();

$_REQUEST['we_transaction'] = isset($_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : $we_transaction;
$_REQUEST['we_transaction'] = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);
print we_html_element::jsElement('
	function doSort(sortitem) {
		entrstr = "";

		top.content.messaging_cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_cmd.php?mcmd=show_folder_content&sort=" + sortitem + entrstr + "&we_transaction=' . $_REQUEST['we_transaction'] . '";
	}') .
	STYLESHEET .
	we_html_element::cssElement('.defaultfont a {color:black; text-decoration:none}');
?>
</head>
<body  background="<?php print IMAGE_DIR; ?>backgrounds/header_with_black_line.gif"  marginwidth="7" marginheight="6" topmargin="6" leftmargin="7">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<?php if(!isset($_REQUEST["viewclass"]) || $_REQUEST["viewclass"] != "todo"){ ?>
				<td width="18"><?php we_html_tools::pPixel(18, 1) ?></td>
				<td class="defaultfont" width="200"><a href="javascript:doSort('subject');"><b><?php echo g_l('modules_messaging', '[subject]') ?></b>&nbsp;<?php echo ( (isset($_REQUEST["si"]) && $_REQUEST["si"] == 'subject') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="170"><a href="javascript:doSort('date');"><b><?php echo g_l('modules_messaging', '[date]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'date') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="120"><a href="javascript:doSort('sender');"><b><?php echo g_l('modules_messaging', '[from]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'sender') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="70"><a href="javascript:doSort('isread');"><b><?php echo g_l('modules_messaging', '[is_read]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'isread') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
			<?php } else{ ?>
				<td width="18"><?php we_html_tools::pPixel(18, 1) ?></td>
				<td class="defaultfont" width="200"><a href="javascript:doSort('subject');"><b><?php echo g_l('modules_messaging', '[subject]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'subject') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="170"><a href="javascript:doSort('deadline');"><b><?php echo g_l('modules_messaging', '[deadline]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'deadline') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="120"><a href="javascript:doSort('priority');"><b><?php echo g_l('modules_messaging', '[priority]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'priority') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="70"><a href="javascript:doSort('status');"><b><?php echo g_l('modules_messaging', '[status]') ?></b>&nbsp;<?php echo ((isset($_REQUEST["si"]) && $_REQUEST["si"] == 'status') ? sort_arrow("arrow_sortorder_" . $_REQUEST['so'], "") : we_html_tools::getPixel(1, 1)) ?></a></td>
			<?php } ?>
		</tr>
	</table>
</body>
</html>
