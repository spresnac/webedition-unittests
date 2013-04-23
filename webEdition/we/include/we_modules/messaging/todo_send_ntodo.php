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
if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);

if(isset($_REQUEST['td_deadline_hour'])){
	$deadline = mktime($_REQUEST['td_deadline_hour'], $_REQUEST['td_deadline_minute'], 0, $_REQUEST['td_deadline_month'], $_REQUEST['td_deadline_day'], $_REQUEST['td_deadline_year']);
}

switch($_REQUEST["mode"]){
	case 'forward':
		$arr = array('rcpts_string' => $_REQUEST['rcpts_string'], 'deadline' => $deadline, 'body' => $_REQUEST['mn_body']);
		$res = $messaging->forward($arr);
		$heading = g_l('modules_messaging', '[forwarding_todo]');
		$action = g_l('modules_messaging', '[forwarded_to]');
		$s_action = g_l('modules_messaging', '[todo_s_forwarded]');
		$n_action = g_l('modules_messaging', '[todo_n_forwarded]');
		break;
	case 'reject':
		$arr = array('body' => $_REQUEST['mn_body']);
		$res = $messaging->reject($arr);
		$heading = g_l('modules_messaging', '[rejecting_todo]');
		$action = g_l('modules_messaging', '[rejected_to]');
		$s_action = g_l('modules_messaging', '[todo_s_rejected]');
		$n_action = g_l('modules_messaging', '[todo_n_rejected]');
		break;
	default:
		$arr = array('rcpts_string' => $_REQUEST['rcpts_string'], 'subject' => $_REQUEST['mn_subject'], 'body' => $_REQUEST['mn_body'], 'deadline' => $deadline, 'status' => 0, 'priority' => $_REQUEST['mn_priority']);
		$res = $messaging->send($arr, "we_todo");
		$heading = g_l('modules_messaging', '[creating_todo]');
		$s_action = g_l('modules_messaging', '[todo_s_created]');
		$n_action = g_l('modules_messaging', '[todo_n_created]');
		break;
}
we_html_tools::htmlTop($heading);
print STYLESHEET . we_html_element::jsElement('
			top.opener.top.content.messaging_cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_cmd.php?mcmd=refresh_mwork&we_transaction=' . $_REQUEST['we_transaction'] . '";');
if(!empty($res['ok'])){
	echo we_html_element::jsElement('
        if (opener && opener.top && opener.top.content) {
		    top.opener.top.content.update_messaging();
		    top.opener.top.content.update_msg_quick_view();
        }');
}
?>
</head>

<body class="weDialogBody">
	<?php
	$res['ok'] = array_map('oldHtmlspecialchars', $res['ok']);
	$res['failed'] = array_map('oldHtmlspecialchars', $res['failed']);
	$res['err'] = array_map('oldHtmlspecialchars', $res['err']);


	$tbl = '<table align="center" cellpadding="7" cellspacing="3">
		    <tr>
		      <td class="defaultfont" valign="top">' . $s_action . ':</td>
		      <td class="defaultfont"><ul><li>' . (empty($res['ok']) ? g_l('modules_messaging', '[nobody]') : implode("</li>\n<li>", $res['ok'])) . '</li></ul></td>
		    </tr>
		    ' . (empty($res['failed']) ? '' : '<tr>
		        <td class="defaultfont" valign="top">' . $n_action . ':</td>
		        <td class="defaultfont"><ul><li>' . implode("</li>\n<li>", $res['failed']) . '</li></ul></td>
		    </tr>') .
		(empty($res['err']) ? '' : '<tr>
		        <td class="defaultfont" valign="top">' . g_l('modules_messaging', '[occured_errs]') . ':</td>
		        <td class="defaultfont"><ul><li>' . implode('</li><li>', $res['err']) . '</li></ul></td>
		    </tr>') . '
	    </table>
	';
	echo we_html_tools::htmlDialogLayout($tbl, $heading, we_button::create_button("ok", "javascript:top.window.close()"), "100%", "30", "", "hidden");
	?>
</body>
</html>