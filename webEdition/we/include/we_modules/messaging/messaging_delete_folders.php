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
include_once(WE_INCLUDES_PATH . 'we_delete_fn.inc.php');

we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET;

if(!preg_match('|^([a-f0-9]){32}$|', $_REQUEST['we_transaction'])){
	exit();
}


$messaging = new we_messaging($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
?>
<script type="text/javascript"><!--
	function we_submitForm(target,url) {
		var f = self.document.we_form;
		var sel = "";
		for(var i=1;i<=top.menuDaten.laenge;i++) {
			if(top.menuDaten[i].checked)
				sel += (top.menuDaten[i].name+",");
		}
		if(!sel) {
			top.toggleBusy(0);
<?php print we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_delete]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
			return;
		}
		sel = sel.substring(0,sel.length-1);
		f.sel.value = sel;
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
	}

	function do_delete() {
		document.we_form.folders.value = top.content.entries_selected.join(",");
		document.we_form.submit();
	}

<?php
if(isset($_REQUEST['mcmd']) && $_REQUEST['mcmd'] == 'delete_folders'){
	$folders = explode(',', $_REQUEST['folders']);

	if($folders[0] != ""){

		$res = $messaging->delete_folders($folders);
		$v = array_shift($res);
		if($v > 0){

			$messaging->saveInSession($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]);
			?>
							top.content.messaging_cmd.location = '<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_cmd.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>&mcmd=delete_folders&folders=<?php echo join(',', $v) ?>';
							top.content.we_cmd('messaging_start_view','','<?php echo isset($_REQUEST['table']) ? $_REQUEST['table'] : '' ?>');
							//-->
			</script>
			</head>
			<body></body>
			</html>
			<?php
			exit;
		} else{
			echo we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[err_delete_folders]'), we_message_reporting::WE_MESSAGE_ERROR);
		}
	}
}
?>

//-->
</script>

<?php
$content = "<span class=\"defaultfont\">" . g_l('modules_messaging', '[deltext]') . "</span>";

$form = '<form name="we_form" method="post">' .
	we_html_tools::hidden('we_transaction', $_REQUEST['we_transaction']) .
	we_html_tools::hidden('folders', '') .
	we_html_tools::hidden('mcmd', 'delete_folders')
	.
	'</form>';

$_buttons = we_button::position_yes_no_cancel(we_button::create_button("ok", "javascript:do_delete()"), "", we_button::create_button("cancel", "javascript:top.content.we_cmd('messaging_start_view')")
);
?>
</head>

<body bgcolor="white" marginwidth="10" marginheight="10" leftmargin="10" topmargin="10" background="<?php echo IMAGE_DIR; ?>msg_white_bg.gif">
	<?php
	echo we_html_tools::htmlMessageBox(400, 120, $content, g_l('modules_messaging', '[rm_folders]'), $_buttons) .
	$form
	?>
</body>

</html>