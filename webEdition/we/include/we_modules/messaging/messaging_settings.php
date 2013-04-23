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
$messaging = new we_messaging($_SESSION['weS']['we_data']['we_messagin_setting']);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data']['we_messagin_setting']);
we_html_tools::htmlTop(g_l('modules_messaging', '[settings]'));
echo we_html_element::jsScript(JS_DIR . 'we_showMessage.js');
?>
<script type="text/javascript">
	<!--
<?php
if(isset($_REQUEST['mcmd']) && $_REQUEST['mcmd'] == 'save_settings' && isset($_REQUEST['check_step'])){
	if($messaging->save_settings(array('check_step' => $_REQUEST['check_step']))){
		print we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE);
		?>
					window.close();
					//-->
		</script>
		</head>
		<body></body>
		</html>
		<?php
		exit;
	}
}
?>
function save() {
document.settings.submit();
}
//-->
</script>

<?php
we_html_tools::protect();

print STYLESHEET;
?>

<body class="weDialogBody">
	<form name="settings" action="<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_settings.php" method="post">
		<?php
		if(isset($_REQUEST['we_transaction'])){
			$_REQUEST['we_transaction'] = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);
			echo we_html_tools::hidden('we_transaction', $_REQUEST['we_transaction']);
		}
		echo we_html_tools::hidden('mcmd', 'save_settings');

		$heading = g_l('modules_messaging', '[settings]');
		$t_vals = array('-1' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '10' => '10', '15' => '15', '30' => '30', '45' => '45', '60' => '60');
		$settings = $messaging->get_settings();
		$check_step = isset($settings['check_step']) ? $settings['check_step'] : "";

		$input_tbl = '<table>
<tr>
    <td class="defaultfont">' . g_l('modules_messaging', '[check_step]') . '</td>
    <td>' . we_html_tools::html_select('check_step', 1, $t_vals, $check_step) . '</td>
	<td class="defaultfont">' . g_l('modules_messaging', '[minutes]') . '</td>
</tr>
</table>';

		$_buttons = we_button::position_yes_no_cancel(we_button::create_button("save", "javascript:save()"), "", we_button::create_button("cancel", "javascript:window.close();")
			)
		;

		echo we_html_tools::htmlDialogLayout($input_tbl, $heading, $_buttons);
		?>
	</form>
</body>
</html>