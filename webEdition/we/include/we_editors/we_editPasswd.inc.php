<?php
/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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

we_html_tools::htmlTop(g_l('global', '[changePass]'));

if(isset($_REQUEST['we_cmd'][1]) && ($_REQUEST['we_cmd'][1] == "content")){
	print STYLESHEET .
		we_html_element::jsElement('
		function add() {
			var p=document.forms[0].elements["we_category"];
		}

		self.focus();');
	?>
	</head>

	<body class="weDialogBody" style="overflow:hidden;text-align:center;">

		<form target="passwdload" action="<?php print WEBEDITION_DIR; ?>we_cmd.php" method="post">
			<?php
			$oldpass = we_html_tools::htmlTextInput("oldpasswd", 20, "", 32, "", "password", 200);
			$newpass = we_html_tools::htmlTextInput("newpasswd", 20, "", 32, "", "password", 200);
			$newpass2 = we_html_tools::htmlTextInput("newpasswd2", 20, "", 32, "", "password", 200);

			$okbut = we_button::create_button("save", "javascript:document.forms[0].submit();");
			$cancelbut = we_button::create_button("cancel", "javascript:top.close();");

			$content = '
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="defaultfont">
									' . g_l('global', '[oldPass]') . '</td>
							</tr>
							<tr>
								<td>
									' . $oldpass . '</td>
							</tr>
							<tr>
								<td>
									' . we_html_tools::getPixel(2, 5) . '</td>
							</tr>
							<tr>
								<td class="defaultfont">
									' . g_l('global', '[newPass]') . '</td>
							</tr>
							<tr>
								<td>
									' . $newpass . '</td>
							</tr>
							<tr>
								<td>
									' . we_html_tools::getPixel(2, 5) . '</td>
							</tr>
							<tr>
								<td class="defaultfont">
									' . g_l('global', '[newPass2]') . '</td>
							</tr>
							<tr>
								<td>
									' . $newpass2 . '</td>
							</tr>
						</table>';


			$_buttons = we_button::position_yes_no_cancel($okbut, null, $cancelbut);

			$frame = we_html_tools::htmlDialogLayout($content, g_l('global', '[changePass]'), $_buttons);
			print $frame;
			print '	<input type="hidden" name="cmd" value="ok" />
							<input type="hidden" name="we_cmd[0]" value="' . $_REQUEST['we_cmd'][0] . '" />
							<input type="hidden" name="we_cmd[1]" value="load" />';
			?>
		</form>
		<?php
	} else if(isset($_REQUEST['we_cmd'][1]) && ($_REQUEST['we_cmd'][1] == "load")){
		$oldpasswd = isset($_REQUEST["oldpasswd"]) ? $_REQUEST["oldpasswd"] : "";
		$newpasswd = isset($_REQUEST["newpasswd"]) ? $_REQUEST["newpasswd"] : "";
		$newpasswd2 = isset($_REQUEST["newpasswd2"]) ? $_REQUEST["newpasswd2"] : "";
		?>
		<script type="text/javascript"><!--
	<?php
	if(isset($_REQUEST["cmd"]) && ($_REQUEST["cmd"] == "ok")){
		$userData = getHash('SELECT UseSalt,passwd FROM ' . USER_TABLE . ' WHERE username="' . $DB_WE->escape($_SESSION["user"]["Username"]) . '"', $DB_WE);

		if(!we_user::comparePasswords($userData['UseSalt'], $_SESSION["user"]["Username"], $userData['passwd'], $oldpasswd)){
			print we_message_reporting::getShowMessageCall(g_l('global', '[pass_not_match]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	top.passwdcontent.document.forms[0].elements["oldpasswd"].focus();
	top.passwdcontent.document.forms[0].elements["oldpasswd"].select();';
		} else if(strlen($newpasswd) < 4){
			print we_message_reporting::getShowMessageCall(g_l('global', '[pass_to_short]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	top.passwdcontent.document.forms[0].elements["newpasswd"].focus();
	top.passwdcontent.document.forms[0].elements["newpasswd"].select();';
		} else if($newpasswd != $newpasswd2){
			print we_message_reporting::getShowMessageCall(g_l('global', '[pass_not_confirmed]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	top.passwdcontent.document.forms[0].elements["newpasswd2"].focus();
	top.passwdcontent.document.forms[0].elements["newpasswd2"].select();';
		} else{
			$useSalt = 0;
			//essential leave this line
			$pwd = $DB_WE->escape(we_user::makeSaltedPassword($useSalt, $_SESSION["user"]["Username"], $newpasswd));
			$DB_WE->query('UPDATE ' . USER_TABLE . ' SET passwd="' . $pwd . '", UseSalt=' . $useSalt . ' WHERE ID=' . $_SESSION["user"]['ID'] . ' AND username="' . $DB_WE->escape($_SESSION["user"]["Username"]) . '"');
			print we_message_reporting::getShowMessageCall(g_l('global', '[pass_changed]'), we_message_reporting::WE_MESSAGE_NOTICE) .
				'top.close();';
		}
	}
	?>
		//-->
		</script>
	</head>

	<body>
		<?php
	} else{

		print we_html_element::jsScript(JS_DIR . "keyListener.js") .
			we_html_element::jsElement("
					function saveOnKeyBoard() {
						window.frames[0].document.forms[0].submit();
						return true;
					}
					function closeOnEscape() {
						return true;

					}
				  ");
		?>
	</head>

	<frameset rows="*,0" framespacing="0" border="0" frameborder="NO">
		<frame src="<?php print WEBEDITION_DIR ?>we_cmd.php?we_cmd[0]=<?php print isset($_REQUEST['we_cmd'][0]) ? $_REQUEST['we_cmd'][0] : ""; ?>&we_cmd[1]=content" name="passwdcontent" noresize/>
		<frame src="<?php print WEBEDITION_DIR ?>we_cmd.php?we_cmd[0]=<?php print isset($_REQUEST['we_cmd'][0]) ? $_REQUEST['we_cmd'][0] : ""; ?>&we_cmd[1]=load" name="passwdload" noresize>/
	</frameset>

	<body>
		<?php
	}
	?>
</body>

</html>