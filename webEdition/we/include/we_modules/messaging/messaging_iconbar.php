<?php
/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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
include_once(WE_MESSAGING_MODULE_PATH . "msg_html_tools.inc.php");
we_html_tools::protect();
we_html_tools::htmlTop();

if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}

$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);

print STYLESHEET;
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--

	function new_message(mode) {
		if (mode == 're' && (top.content.messaging_main.messaging_right.msg_work.last_entry_selected == -1)) {
			return;
		}

		new jsWindow("<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_newmessage.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&mode=" + mode, "messaging_new_message",-1,-1,670,530,true,false,true,false);
	}

	function copy_messages() {

		if (top.content.messaging_main.messaging_right.msg_work.entries_selected && top.content.messaging_main.messaging_right.msg_work.entries_selected.length > 0) {
			top.content.messaging_cmd.location = "<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&mcmd=copy_msg&entrsel=" + top.content.messaging_main.messaging_right.msg_work.entries_selected.join(',');
		}
	}

	function cut_messages() {

		if (top.content.messaging_main.messaging_right.msg_work.entries_selected && top.content.messaging_main.messaging_right.msg_work.entries_selected.length > 0) {
			top.content.messaging_cmd.location = "<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&mcmd=cut_msg&entrsel=" + top.content.messaging_main.messaging_right.msg_work.entries_selected.join(',');
		}
	}

	function paste_messages() {
		if (top.content.messaging_main.messaging_right.msg_work.entries_selected) {
			top.content.messaging_cmd.location = "<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&mcmd=paste_msg&entrsel=" + top.content.messaging_main.messaging_right.msg_work.entries_selected.join(',');
		}
	}

	function delete_messages() {
		if (top.content.messaging_main.messaging_right.msg_work.entries_selected && top.content.messaging_main.messaging_right.msg_work.entries_selected.length > 0) {
			c = confirm("<?php echo g_l('modules_messaging', '[q_rm_messages]') ?>");
			if (c == false) {
				return;
			}
			top.content.messaging_cmd.location = "<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&mcmd=delete_msg&entrsel=" + top.content.messaging_main.messaging_right.msg_work.entries_selected.join(',');
		}
	}

	function refresh() {
		top.content.update_messaging();
		top.content.update_msg_quick_view();
	}

	function launch_todo() {
		if (top.content.messaging_main.messaging_right.msg_work.entries_selected) {
			top.content.messaging_cmd.location = '<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?mcmd=launch&mode=todo&we_transaction=<?php echo $_REQUEST['we_transaction'] ?>';
		}
	}

	//-->
</script>
</head>

<body background="<?php print IMAGE_DIR ?>backgrounds/iconbarBack.gif" marginwidth="0" topmargin="5" marginheight="5" leftmargin="0">
	<table border="0" cellpadding="8" cellspacing="0" width="100%">
		<tr>
			<td width="36">
<?php echo we_button::create_button("image:btn_messages_create", "javascript:new_message('new')", true); ?></td>
			<td width="36">
				<?php echo we_button::create_button("image:btn_messages_reply", "javascript:new_message('re')", true); ?></td>
			<td width="36">
			</td>
			<td width="36">
<?php echo we_button::create_button("image:btn_messages_copy", "javascript:copy_messages()", true); ?></td>
			<td width="36">
				<?php echo we_button::create_button("image:btn_messages_cut", "javascript:cut_messages()", true); ?></td>
			<td width="36">
				<?php echo we_button::create_button("image:btn_messages_paste", "javascript:paste_messages()", true); ?></td>
			<td width="36">
				<?php echo we_button::create_button("image:btn_messages_trash", "javascript:delete_messages()", true); ?></td>
			<td width="36">
				<?php echo we_button::create_button("image:btn_messages_update", "javascript:refresh()", true); ?></td>
			<td align="right">
				<?php echo we_button::create_button("image:btn_messages_tasks", "javascript:launch_todo();", true); ?></td>
		</tr>
	</table>
</body>

</html>