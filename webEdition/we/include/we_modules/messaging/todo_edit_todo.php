<?php
/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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

we_html_tools::htmlTop(g_l('modules_messaging', '[wintitle]'));

$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);

$mode = isset($_REQUEST["mode"]) ? $_REQUEST["mode"] : '';
if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}

print STYLESHEET;
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>

<script type="text/javascript"><!--
	rcpt_sel = new Array();

	function update_rcpts() {
		var rcpt_str = rcpt_sel[0][2];
		document.compose_form.mn_recipients.value = rcpt_str;
	}

	function selectRecipient() {

		var rs = escape(document.compose_form.mn_recipients.value);

		new jsWindow("<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_usel.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&maxsel=1&rs=" + rs,"messaging_usel",-1,-1,530,420,true,false,true,false);
	}

	function do_send() {
<?php if($mode != 'reject'){ ?>
					rcpt_s = escape(document.compose_form.mn_recipients.value);
					document.compose_form.rcpts_string.value = rcpt_s;
<?php } ?>
				document.compose_form.submit();
			}

			function doUnload() {
				if(jsWindow_count) {
					for(i=0;i<jsWindow_count;i++) {
						eval("jsWindow"+i+"Object.close()");
					}
				}
			}
			//-->
</script>
</head>

<body class="weDialogBody" <?php echo ($mode == 'reject' ? '' : 'onLoad="document.compose_form.mn_subject.focus()"') ?> onUnload="doUnload();">
<?php
if($mode == 'forward'){
	$compose = new we_format('forward', $messaging->selected_message);
	$heading = g_l('modules_messaging', '[forward_todo]');
} else if($mode == 'reject'){
	$compose = new we_format('reject', $messaging->selected_message);
	$heading = g_l('modules_messaging', '[reject_todo]');
} else{
	$compose = new we_format('new');
	$heading = g_l('modules_messaging', '[new_todo]');
}
$compose->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
?>
	<form action="<?php print WE_MESSAGING_MODULE_DIR; ?>todo_send_ntodo.php" name="compose_form" method="post">
	<?php
	echo we_html_tools::hidden('we_transaction', $_REQUEST['we_transaction']);
	echo we_html_tools::hidden('rcpts_string', '');
	echo we_html_tools::hidden('mode', $mode);

	if($mode == 'reject'){
		$tbl = '
					<table cellpadding="6">
					    <tr>
							<td class="defaultgray">
								' . g_l('modules_messaging', '[from]') . ':</td>
							<td class="defaultfont">
								' . $compose->get_from() . '</td>
						</tr>
						<tr>
							<td class="defaultgray">
								' . g_l('modules_messaging', '[reject_to]') . ':</a></td>
							<td class="defaultfont">
								' . $compose->get_recipient_line() . '</td>
						</tr>
						<tr>
							<td class="defaultgray">
								' . g_l('modules_messaging', '[subject]') . ':</td>
							<td class="defaultfont">
								' . oldHtmlspecialchars($compose->get_subject()) . '</td>
						</tr>
					</table>
					<table cellpadding="6">';
	} else{
		$tbl = '
					<table cellpadding="6">
						<tr>
							<td class="defaultgray">
								' . g_l('modules_messaging', '[assigner]') . ':</td>
							<td class="defaultfont">
								' . $compose->get_from() . '</td>
						</tr>
						<tr>
							<td class="defaultgray">
								<a href="javascript:selectRecipient()">' . g_l('modules_messaging', '[recipient]') . ':</a></td>
							<td>
								' . we_html_tools::htmlTextInput('mn_recipients', 40, ($mode == 'forward' ? '' : $_SESSION["user"]["Username"])) . '</td>
						</tr>
						<tr>
							<td class="defaultgray">
								' . g_l('modules_messaging', '[subject]') . ':</td>
							<td>
								' . we_html_tools::htmlTextInput('mn_subject', 40, $compose->get_subject()) . '</td>
						</tr>
						<tr>
							<td class="defaultgray">
								' . g_l('modules_messaging', '[deadline]') . ':</td>
							<td>
								' . we_html_tools::getDateInput2('td_deadline%s', $compose->get_deadline()) . '</td>
						</tr>
						<tr>
							<td class="defaultgray">' . g_l('modules_messaging', '[priority]') . ':</td>
							<td>' . we_html_tools::html_select('mn_priority', 1, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10)) . '</td>
						</tr>
					</table>
					<table cellpadding="6">';
	}
	if($mode != 'new'){
		$tbl .= '
					<tr>
						<td class="defaultfont">' . $compose->get_msg_text() . '</td>
					</tr>
					<tr>
						<td class="defaultfont">' . $compose->get_todo_history() . '</td>
					</tr>';
	}
	$tbl .= '
					<tr>
						<td>
							<textarea cols="68" rows="10" name="mn_body" style="width:624px"></textarea></td>
					</tr>
				</table>';
	$buttons = we_button::position_yes_no_cancel(we_button::create_button("ok", "javascript:do_send()"), "", we_button::create_button("cancel", "javascript:top.window.close()")
	);
	echo we_html_tools::htmlDialogLayout($tbl, "<div style='padding:6px'>" . $heading . "</div>", $buttons, "100", "24");
	?>
	</form>
</body>
</html>