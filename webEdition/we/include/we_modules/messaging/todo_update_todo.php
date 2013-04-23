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

if (!preg_match('|^([a-f0-9]){32}$|i',$_REQUEST['we_transaction'])) {
	exit();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');
include_once(WE_MESSAGING_MODULE_PATH . "msg_html_tools.inc.php");

we_html_tools::protect();

we_html_tools::htmlTop(g_l('modules_messaging','[wintitle]').' - Update Status');

$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);

print STYLESHEET;

?>
	<script type="text/javascript"><!--
	function do_confirm() {
		document.update_todo_form.submit();
	}

	function doUnload() {
		if(top.jsWindow_count){
			for(i=0;i<top.jsWindow_count;i++){
				eval("jsWindow"+i+"Object.close()");
			}
		}
	}
	//-->
	</script>
	</head>
	<body class="weDialogBody"  onUnload="doUnload();">
<?php
$heading = g_l('modules_messaging','[todo_status_update]');
$compose = new we_format('update', $messaging->selected_message);
$compose->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);

?>
    <form action="<?php print WE_MESSAGING_MODULE_DIR; ?>todo_update.php" name="update_todo_form" method="post">
<?php
	echo we_html_tools::hidden('we_transaction', $_REQUEST['we_transaction']);
	echo we_html_tools::hidden('rcpts_string', '');
	echo we_html_tools::hidden('mode', $_REQUEST['mode']);

	$parts = array();
	array_push	($parts, array(	"headline"	=> g_l('modules_messaging','[assigner]'),
								"html"		=> $compose->get_from(),
								"space"		=> 120,
								"noline"	=> 1
								)
				);
	array_push	($parts, array(	"headline"	=> g_l('modules_messaging','[subject]'),
								"html"		=> $compose->get_subject(),
								"space"		=> 120,
								"noline"	=> 1
								)
				);
	array_push	($parts, array(	"headline"	=> g_l('modules_messaging','[deadline]'),
								"html"		=> we_html_tools::getDateInput2('td_deadline%s', $compose->get_deadline()),
								"space"		=> 120,
								"noline"	=> 1
								)
				);
	array_push	($parts, array(	"headline"	=> g_l('modules_messaging','[status]'),
								"html"		=> we_html_tools::htmlTextInput('todo_status', 4, $messaging->selected_message['hdrs']['status']) . ' %',
								"space"		=> 120,
								"noline"	=> 1
								)
				);$prio = $compose->get_priority();
	array_push	($parts, array(	"headline"	=> g_l('modules_messaging','[priority]'),
								"html"		=> we_html_tools::html_select('todo_priority', 1, array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), $compose->get_priority()),
								"space"		=> 120,
								)
				);

	//	message
	array_push	($parts, array(	"headline"	=> "",
								"html"		=> $compose->get_msg_text(),
								"space"		=> 0,
								"noline"	=> 1
								)
				);
	array_push	($parts, array(	"headline"	=> "",
								"html"		=> $compose->get_todo_history(),
								"space"		=> 0,
								)
				);

	array_push	($parts, array(	"headline"	=> g_l('modules_messaging','[comment]'),
								"html"		=> '<textarea cols="40" rows="8" name="todo_comment"></textarea>',
								"space"		=> 120,
								)
				);

	$buttons = we_button::position_yes_no_cancel(	we_button::create_button("ok", "javascript:do_confirm();"),
													"",
													we_button::create_button("cancel", "javascript:top.window.close()")
												);
	print we_multiIconBox::getHTML("todoStatusUpdate", "100%", $parts, 30, $buttons, -1, "", "", false, $heading);
?>
	</form>
	</body>
</html>