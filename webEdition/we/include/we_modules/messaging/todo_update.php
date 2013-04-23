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

if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}

we_html_tools::protect();

$heading = 'ToDo Status-update ...';

$deadline = mktime($_REQUEST['td_deadline_hour'], $_REQUEST['td_deadline_minute'], 0, $_REQUEST['td_deadline_month'], $_REQUEST['td_deadline_day'], $_REQUEST['td_deadline_year']);
$arr = array('deadline' => $deadline);

$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);


if($_REQUEST['todo_status'] != $messaging->selected_message['hdrs']['status']){
	$arr['todo_status'] = $_REQUEST['todo_status'];
}

if(!empty($_REQUEST['todo_comment'])){
	$arr['todo_comment'] = $_REQUEST['todo_comment'];
}

$arr['todo_priority'] = $_REQUEST['todo_priority'];

$res = $messaging->used_msgobjs['we_todo']->update_status($arr, $messaging->selected_message['int_hdrs']);

$messaging->get_fc_data($messaging->Folder_ID, '', '', 0);

$messaging->saveInSession($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
we_html_tools::htmlTop($heading);
print STYLESHEET . we_html_element::jsElement('
			if (opener && opener.top && opener.top.content) {
				top.opener.top.content.update_messaging();
				top.opener.top.content.update_msg_quick_view();
			}');
?>
</head>

<body class="weDialogBody">
	<?php
	$tbl = '<table align="center" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="defaultfont" align="center">
							' . $res['msg'] . '</td>
					</tr>
				</table>';
	echo we_html_tools::htmlDialogLayout($tbl, $heading, we_button::create_button("ok", "javascript:top.window.close()"), "100%", "30", "", "hidden");
	?>
</body>

</html>