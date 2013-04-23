<?php
/**
 * webEdition CMS
 *
 * $Rev: 5815 $
 * $Author: mokraemer $
 * $Date: 2013-02-14 19:31:26 +0100 (Thu, 14 Feb 2013) $
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
we_html_tools::protect();
if($cmd == "ok"){
	$wf_text = $_REQUEST["wf_text"];
	$wf_select = isset($_REQUEST["wf_select"]) ? $_REQUEST["wf_select"] : "";

	$force = (!we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]));

	$ok = we_workflow_utility::approve($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"], $wf_text, $force);

	if($ok){
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_ok]');
		$msgType = we_message_reporting::WE_MESSAGE_NOTICE;

		//	in SEEM-Mode back to Preview page
		if($_SESSION['weS']['we_mode'] == "seem"){
			$script = "opener.top.we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
		} else if($_SESSION['weS']['we_mode'] == "normal"){
			$script = 'opener.top.weEditorFrameController.getActiveDocumentReference().frames[3].location.reload();';
		}

		if(($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == WE_EDITPAGE_INFO)){
			$script .= 'opener.top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // wird in Templ eingef�gt
		}
	} else{
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_notok]');
		$msgType = we_message_reporting::WE_MESSAGE_ERROR;
		//	in SEEM-Mode back to Preview page
		if($_SESSION['weS']['we_mode'] == "seem"){

			$script = "opener.top.we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
		} else if($_SESSION['weS']['we_mode'] == "normal"){

			$script = '';
		}
	}
	print we_html_element::jsElement($script . we_message_reporting::getShowMessageCall($msg, $msgType) . ';top.close();');
}
print STYLESHEET;
?>
</head>
<body class="weDialogBody"><center>
<?php if($cmd == "ok"){

} else{
	?>
			<form action="<?php print WEBEDITION_DIR; ?>we_cmd.php" method="post">
				<?php
				$okbut = we_button::create_button("ok", "javascript:document.forms[0].submit()");
				$cancelbut = we_button::create_button("cancel", "javascript:top.close()");


				$content = '<table border="0" cellpadding="0" cellspacing="0">';
				$wf_textarea = '<textarea name="wf_text" rows="7" cols="50" style="left:10px;right:10px;height:190px"></textarea>';
				$content .= '<tr>
<td class="defaultfont">' . g_l('modules_workflow', '[message]') . '</td>
</tr>
<tr>
<td>' . $wf_textarea . '</td>
</tr>
</table>';

				print we_html_tools::htmlDialogLayout($content, g_l('modules_workflow', '[pass_workflow]'), we_button::position_yes_no_cancel($okbut, "", $cancelbut)).'
<input type="hidden" name="cmd" value="ok" />
<input type="hidden" name="we_cmd[0]" value="' . $_REQUEST['we_cmd'][0] . '" />
<input type="hidden" name="we_cmd[1]" value="' . $we_transaction . '" />';
				?>
			</form>
			<?php } ?>
	</center>
</body>
</html>