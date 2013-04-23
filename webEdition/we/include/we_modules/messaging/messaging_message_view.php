<?php
/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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

if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}

$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->get_mv_data($_REQUEST["id"]);
$messaging->saveInSession($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);

if(empty($messaging->selected_message)){
	exit;
}

$format = new we_format('view', $messaging->selected_message);
$format->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);

we_html_tools::htmlTop();

we_html_tools::protect();

print STYLESHEET;
?>
<style>
	.quote_lvl_0 {}
	.quote_lvl_1 {color:#ff0000}
	.quote_lvl_2 {color:#00ff00}
	.quote_lvl_3 {color:#0000ff}
</style>
<script type="text/javascript"><!--
	function todo_markdone() {
		top.content.messaging_cmd.location = '<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?mcmd=todo_markdone&we_transaction=<?php echo $_REQUEST['we_transaction'] ?>';
	}
	//-->
</script>
</head>
<body class="weDialogBody">

	<?php
	if(isset($messaging->selected_message['hdrs']['ClassName']) && $messaging->selected_message['hdrs']['ClassName'] == 'we_todo'){
		$parts = array(
			array("headline" => g_l('modules_messaging', '[subject]'),
				"html" => "<b>" . oldHtmlspecialchars($format->get_subject()) . "</b>",
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[deadline]'),
				"html" => $format->get_deadline(),
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[status]'),
				"html" => '<table border="0" cellpadding="0" cellspacing="0"><tr><td class="defaultfont">' . $messaging->selected_message['hdrs']['status'] . '%</td><td>' . we_html_tools::getPixel(20, 2) .
				(($messaging->selected_message['hdrs']['status'] < 100) ? '<td>' . we_button::create_button(
						"percent100", "javascript:todo_markdone()") . '</td>' : '') . '</tr></table>',
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[created_by]'),
				"html" => $format->get_from(),
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[assigned_by]'),
				"html" => $format->get_assigner(),
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[creation_date]'),
				"html" => $format->get_date(),
				"space" => 140
			),
			array("headline" => "",
				"html" => $format->get_msg_text(),
				"space" => 0
			)
		);

		if(isset($messaging->selected_message['hdrs']['ClassName']) && $messaging->selected_message['hdrs']['ClassName'] == 'we_todo' && ($h = $format->get_todo_history())){
			array_push($parts, array("headline" => "",
				"html" => $format->get_todo_history(),
				"noline" => 1,
				"space" => 0
				)
			);
		}
	} else{ //	Message
		$parts = array(
			array("headline" => g_l('modules_messaging', '[subject]'),
				"html" => "<b>" . oldHtmlspecialchars($format->get_subject()) . "</b>",
				"noline" => 1,
				"space" => 80
			),
			array("headline" => g_l('modules_messaging', '[from]'),
				"html" => $format->get_from(),
				"noline" => 1,
				"space" => 80
			),
			array("headline" => g_l('modules_messaging', '[date]'),
				"html" => $format->get_date(),
				"noline" => (empty($messaging->selected_message['hdrs']['To']) ? null : 1),
				"space" => 80
			)
		);

		if(!empty($messaging->selected_message['hdrs']['To'])){
			$parts[] = array("headline" => g_l('modules_messaging', '[recipients]'),
				"html" => oldHtmlspecialchars($messaging->selected_message['hdrs']['To']),
				"space" => 80
			);
		}

		$parts[] = array("headline" => "",
			"html" => $format->get_msg_text(),
			"noline" => 1,
			"space" => 0
		);
	}

	print we_multiIconBox::getJS() .
		we_multiIconBox::getHTML("weMessageView", "100%", $parts, 30, "", -1, "", "", false, (isset($messaging->selected_message['hdrs']['ClassName']) && $messaging->selected_message['hdrs']['ClassName'] == 'we_todo' ? g_l('modules_messaging', "[type_todo]") : g_l('modules_messaging', "[type_message]")));
	?>
</body>
</html>
