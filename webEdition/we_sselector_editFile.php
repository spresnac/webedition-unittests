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

if(!we_hasPerm("BROWSE_SERVER"))
	exit();

we_html_tools::htmlTop();

print STYLESHEET;

$we_fileData = "";

if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "save"){
	if(isset($_REQUEST["editFile"])){
		weFile::save($_REQUEST["id"], $_REQUEST["editFile"]);
	}
	$we_fileData = stripslashes($_REQUEST["editFile"]);
} else if(isset($_REQUEST["id"])){

	$_REQUEST["id"] = str_replace("//", "/", $_REQUEST["id"]);
	$we_fileData= weFile::load($_REQUEST["id"]);
	if($we_fileData===false){
		$we_alerttext = sprintf(g_l('alert', "[can_not_open_file]"), str_replace(str_replace("\\", "/", dirname($_REQUEST["id"])) . "/", "", $_REQUEST["id"]), 1);
	}
}

$buttons = we_button::position_yes_no_cancel(
		we_button::create_button("save", "javascript:document.forms[0].submit();"), null, we_button::create_button("cancel", "javascript:self.close();")
);
$content = '<textarea name="editFile" id="editFile" style="width:540px;height:380px;overflow: auto;">' . oldHtmlspecialchars($we_fileData) . '</textarea>';
?>
<script type="text/javascript"><!--
	function setSize(){
		var ta = document.getElementById("editFile");
		ta.style.width=document.body.offsetWidth-60;
		ta.style.height=document.body.offsetHeight-118;
	}
<?php if(isset($we_alerttext)){
	print we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR); ?>
			self.close();
<?php } ?>
	self.focus();
<?php if(isset($_REQUEST["editFile"]) && (!isset($we_alerttext))){ ?>
		opener.top.fscmd.selectDir();
		self.close();
<?php } ?>
	//-->
</script>
</head>
<body class="weDialogBody" onResize="setSize()" style="width:100%; height:100%"><center>
		<form method="post">
			<input type="hidden" name="cmd" value="save" />
<?php print we_html_tools::htmlDialogLayout($content, g_l('global', '[edit_file]') . ": <span class=\"weMultiIconBoxHeadline\">" . str_replace(str_replace("\\", "/", dirname($_REQUEST["id"])) . "/", "", $_REQUEST["id"]), $buttons, 1) . "</span>"; ?>
		</form></center>
</body>
</html>
