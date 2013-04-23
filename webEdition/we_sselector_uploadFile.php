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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET;

$cpat = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"]);

function weFile($f){
	return f("SELECT 1 AS a FROM " . FILE_TABLE . " WHERE Path='" . $GLOBALS['DB_WE']->escape($f) . "'", 'a', $GLOBALS['DB_WE']) === '1';
}

$we_alerttext = "";

if(isset($_FILES['we_uploadFile'])){
	$overwrite = $_REQUEST["overwrite"];
	$tempName = TEMP_PATH . "/" . weFile::getUniqueId();
	move_uploaded_file($_FILES['we_uploadFile']["tmp_name"], $tempName);
	if(file_exists($cpat . "/" . $_FILES['we_uploadFile']["name"])){
		if($overwrite == "yes"){
			if(weFile($_REQUEST["pat"] . "/" . $_FILES['we_uploadFile']["name"])){
				$we_alerttext = g_l('fileselector', "[can_not_overwrite_we_file]");
			}
		} else{
			$z = 0;

			if(preg_match('|^(.+)(\.[^\.]+)$|', $_FILES['we_uploadFile']["name"], $regs)){
				$extension = $regs[2];
				$filename = $regs[1];
			} else{
				$extension = "";
				$filename = $_FILES['we_uploadFile']["name"];
			}

			$footext = $filename . "_" . $z . $extension;
			while(file_exists($cpat . "/" . $footext)) {
				$z++;
				$footext = $filename . "_" . $z . $extension;
			}
			$_FILES['we_uploadFile']["name"] = $footext;
		}
	}
	if(!$we_alerttext){
		copy($tempName, str_replace("\\", "/", str_replace("//", "/", $cpat . "/" . $_FILES['we_uploadFile']["name"])));
	}
	we_util_File::deleteLocalFile($tempName);
}
$maxsize = getUploadMaxFilesize(false);


$yes_button = we_button::create_button("upload", "javascript:if(!document.forms['we_form'].elements['we_uploadFile'].value) { " . we_message_reporting::getShowMessageCall(g_l('fileselector', "[edit_file_nok]"), we_message_reporting::WE_MESSAGE_ERROR) . "} else document.forms['we_form'].submit();");
$cancel_button = we_button::create_button("cancel", "javascript:self.close();");
$buttons = we_button::position_yes_no_cancel($yes_button, null, $cancel_button);

$content = '<table border="0" cellpadding="0" cellspacing="0">' .
	($maxsize ? ('<tr><td>' . we_html_tools::htmlAlertAttentionBox(
			sprintf(g_l('newFile', "[max_possible_size]"), round($maxsize / (1024 * 1024), 3) . "MB"), 1, 390) . '</td></tr><tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>') : '') . '
			<tr><td><input name="we_uploadFile" TYPE="file" size="35" /></td></tr><tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>
			<tr><td class="defaultfont">' . g_l('newFile', "[caseFileExists]") . '</td></tr><tr><td>' .
	we_forms::radiobutton("yes", true, "overwrite", g_l('newFile', "[overwriteFile]")) .
	we_forms::radiobutton("no", false, "overwrite", g_l('newFile', "[renameFile]")) . '</td></tr></table>';

$content = we_html_tools::htmlDialogLayout($content, g_l('newFile', "[import_File_from_hd_title]"), $buttons);
?>
<script type="text/javascript"><!--
	self.focus();
<?php if(isset($_FILES['we_uploadFile']) && (!$we_alerttext)){ ?>
		opener.top.fscmd.selectFile('<?php print $_FILES['we_uploadFile']["name"]; ?>');
		opener.top.fscmd.selectDir();
		self.close();
<?php
} elseif($we_alerttext){
	print we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
}
?>
	//-->
</script>
</head>
<body class="weDialogBody" onLoad="self.focus();"><center>
		<input type="hidden" name="pat" value="<?php print $_REQUEST["pat"]; ?>" />
		<form method="post" enctype="multipart/form-data" name="we_form">
<?php print $content; ?>
		</form>
	</center>
</body>
</html>
