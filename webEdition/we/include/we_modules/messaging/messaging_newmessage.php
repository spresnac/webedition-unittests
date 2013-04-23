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

we_html_tools::protect();

we_html_tools::htmlTop('Messaging System - ' . g_l('modules_messaging', '[new_message]'));

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
	rcpt_sel = new Array();

	function update_rcpts() {
		var rcpt_str = "";

		for (i = 0; i < rcpt_sel.length; i++) {
			rcpt_str += rcpt_sel[i][2];
			if (i != rcpt_sel.length - 1) {
				rcpt_str += ", ";
			}
		}

		document.compose_form.mn_recipients.value = rcpt_str;
	}

	function selectRecipient() {
		var rs = escape(document.compose_form.mn_recipients.value);

		new jsWindow("<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_usel.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&rs=" + rs,"messaging_usel",-1,-1,530,420,true,false,true,false);
		//	    opener.top.add_win(msg_usel);
	}

	function do_send() {
		rcpt_s = escape(document.compose_form.mn_recipients.value);
		document.compose_form.rcpts_string.value = rcpt_s;
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

<body class="weDialogBody" onLoad="document.compose_form.mn_body.focus()" onUnload="doUnload();">
<?php
if($_REQUEST["mode"] == 're'){
	$compose = new we_format('re', $messaging->selected_message);
	$heading = g_l('modules_messaging', '[reply_message]');
} else{
	if(substr($_REQUEST["mode"], 0, 2) == 'u_'){
		$_u = str_replace(substr($_REQUEST["mode"], 0, 2), '', $_REQUEST["mode"]);
	}
	$compose = new we_format('new');
	$heading = g_l('modules_messaging', '[new_message]');
}

$compose->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
?>
  <form action="<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_send_nm.php" name="compose_form" method="post">
	<?php
	echo we_html_tools::hidden('we_transaction', $_REQUEST['we_transaction']);
	echo we_html_tools::hidden('rcpts_string', '');
	echo we_html_tools::hidden('mode', $_REQUEST["mode"]);

	$tbl = '<table align="center" cellpadding="6" width="100%">
      <tr><td class="defaultgray">' . g_l('modules_messaging', '[from]') . ':</td><td class="defaultfont">' . $compose->get_from() . '</td></tr>
      <tr><td class="defaultgray"><a href="javascript:selectRecipient()">' . g_l('modules_messaging', '[recipients]') . ':</a></td><td>' . we_html_tools::htmlTextInput('mn_recipients', 40, (!isset($_u) ? $compose->get_recipient_line() : $_u)) . '</td></tr>
      <tr><td class="defaultgray">' . g_l('modules_messaging', '[subject]') . ':</td><td>' . we_html_tools::htmlTextInput('mn_subject', 40, $compose->get_subject()) . '</td></tr>
      <tr><td colspan="2"><textarea cols="68" rows="15" name="mn_body" style="width:605px">' . $compose->get_msg_text() . '</textarea></td></tr>
    </table>';

	$_buttons = we_button::position_yes_no_cancel(we_button::create_button("ok", "javascript:do_send()"), "", we_button::create_button("cancel", "javascript:window.close()")
	);

	echo we_html_tools::htmlDialogLayout($tbl, "<div style='padding:6px'>" . $heading . "</div>", $_buttons, "100%", "24", "", "hidden");
	?>
	</form>
</body>
</html>