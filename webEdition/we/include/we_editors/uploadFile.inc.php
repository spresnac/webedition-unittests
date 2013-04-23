<?php
/**
 * webEdition CMS
 *
 * $Rev: 5663 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 23:48:43 +0100 (Tue, 29 Jan 2013) $
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

// init document
$we_alerttext = "";
$allowedContentTypes = "";
$error = false;

$maxsize = getUploadMaxFilesize(false);
$we_maxfilesize_text = sprintf(g_l('newFile', "[max_possible_size]"), round($maxsize / (1024 * 1024), 3) . "MB");


we_html_tools::htmlTop(g_l('newFile', "[import_File_from_hd_title]"));

print STYLESHEET;

if(!isset($_SESSION['weS']['we_data'][$we_transaction])){
	$we_alerttext = $we_maxfilesize_text;
	$error = true;
} else{

	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
	include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

	switch($we_doc->ContentType){
		case "image/*";
			$allowedContentTypes = we_image_edit::IMAGE_CONTENT_TYPES;
			break;
		case "application/*";
			break;
		default:
			$allowedContentTypes = $we_doc->ContentType;
	}

	if(isset($_FILES["we_File"]) && $_FILES["we_File"]["name"] != "" && $_FILES['we_File']["type"] && (($allowedContentTypes == "") || (!(strpos($allowedContentTypes, $_FILES['we_File']["type"]) === false)))){
		$we_File = TEMP_PATH . '/' . weFile::getUniqueId();
		move_uploaded_file($_FILES["we_File"]["tmp_name"], $we_File);
		if((!$we_doc->Filename) || (!$we_doc->ID)){
			// Bug Fix #6284
			$we_doc->Filename = preg_replace("/[^A-Za-z0-9._-]/", "", $_FILES["we_File"]["name"]);
			$we_doc->Filename = preg_replace('/^(.+)\..+$/', "\\1", $we_doc->Filename);
		}

		$foo = explode("/", $_FILES["we_File"]["type"]);
		$we_doc->setElement("data", $we_File, $foo[0]);
		if($we_doc->ContentType == "image/*" || $we_doc->ContentType == "application/x-shockwave-flash"){
			$we_size = $we_doc->getimagesize($we_File);
			$we_doc->setElement("width", $we_size[0], "attrib");
			$we_doc->setElement("height", $we_size[1], "attrib");
			$we_doc->setElement("origwidth", $we_size[0]);
			$we_doc->setElement("origheight", $we_size[1]);
		}
		$we_doc->Extension = strtolower((strpos($_FILES["we_File"]["name"], ".") > 0) ? preg_replace('/^.+(\..+)$/', "\\1", $_FILES["we_File"]["name"]) : ""); //strtolower for feature 3764
		$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
		$we_doc->Path = $we_doc->getPath();
		$we_doc->DocChanged = true;

		if($we_doc->Extension == '.pdf'){
			$we_doc->setMetaDataFromFile($we_File);
		}

		$_SESSION['weS']['we_data']["tmpName"] = $we_File;
		if(isset($_REQUEST["import_metadata"]) && !empty($_REQUEST["import_metadata"])){
			$we_doc->importMetaData();
		}
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
	} else if(isset($_FILES['we_File']['name']) && !empty($_FILES['we_File']['name'])){
		$we_alerttext = g_l('alert', "[wrong_file][" . $we_doc->ContentType . ']');
	} else if(isset($_FILES['we_File']['name']) && empty($_FILES['we_File']['name'])){
		$we_alerttext = g_l('alert', "[no_file_selected]");
	}
}

$content = '<table border="0" cellpadding="0" cellspacing="0">' .
	($maxsize ? ('<tr><td>' . we_html_tools::htmlAlertAttentionBox(
			$we_maxfilesize_text, 1, 390) . '</td></tr><tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>') : '') . '
				<tr><td><input name="we_File" TYPE="file"' . ($allowedContentTypes ? ' ACCEPT="' . $allowedContentTypes . '"' : '') . ' size="35" /></td></tr>
				<tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>
';
if($we_doc->ContentType == "image/*"){
	$content .= '<tr><td>' . we_forms::checkbox("1", true, "import_metadata", g_l('metadata', "[import_metadata_at_upload]")) . '</td></tr>
';
}
$content .= '</table>';


$_buttons = we_button::position_yes_no_cancel(we_button::create_button("upload", "javascript:document.forms[0].submit();"), "", we_button::create_button("cancel", "javascript:self.close();")
);
?>


<script type="text/javascript"><!--
<?php
if($we_alerttext){
	print we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
	if($error){
		?>
					top.close();
		<?php
	}
}

if(isset($we_File) && (!$we_alerttext)){
	?>
			opener.we_cmd("update_file");
			_EditorFrame = opener.top.weEditorFrameController.getActiveEditorFrame();
			_EditorFrame.getDocumentReference().frames[0].we_setPath("<?php print $we_doc->Path; ?>","<?php print $we_doc->Text; ?>");
			self.close();
<?php } ?>
	//-->
</script>
</head>

<body class="weDialogBody" onLoad="self.focus();">
	<center>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="we_transaction" value="<?php print $we_transaction ?>" />
			<?php print we_html_tools::htmlDialogLayout($content, g_l('newFile', "[import_File_from_hd_title]"), $_buttons); ?>
		</form>
	</center>
</body>

</html>