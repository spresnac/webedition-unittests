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
// force the download of this document
if(isset($_REQUEST['we_cmd'][3]) && $_REQUEST['we_cmd'][3] == "download"){
	$_filename = $we_doc->Filename . $we_doc->Extension;
	//$_size = filesize($_SERVER['DOCUMENT_ROOT'] . $we_doc->Path);

	if(we_isHttps()){		 // Additional headers to make downloads work using IE in HTTPS mode.
		header("Pragma: ");
		header("Cache-Control: ");
		header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");	 // HTTP 1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
	} else{
		header("Cache-control: private, max-age=0, must-revalidate");
	}

	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="' . trim(htmlentities($_filename)) . '"');
	header('Content-Description: ' . trim(htmlentities($_filename)));
	header('Content-Length: ' . $_SERVER['DOCUMENT_ROOT'] . $we_doc->Path);

	readfile($_SERVER['DOCUMENT_ROOT'] . $we_doc->Path);
	exit;
}


we_html_tools::htmlTop();


if(isset($_REQUEST['we_cmd'][0]) && substr($_REQUEST['we_cmd'][0], 0, 15) == "doImage_convert"){
	print we_html_element::jsElement('parent.frames[0].we_setPath("' . $we_doc->Path . '","' . $we_doc->Text . '", "' . $we_doc->ID . '");');
}

echo we_html_element::jsScript(JS_DIR . 'windows.js');
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

print STYLESHEET;
?>
</head>

<body class="weEditorBody">
	<form name="we_form" method="post">
		<?php
		echo we_class::hiddenTrans();

		switch(strtolower($we_doc->Extension)){
			case ".pdf":
				$previewAvailable = true;
				break;
			default:
				$previewAvailable = false;
				break;
		}

		if($previewAvailable && $we_doc->ID){
			?>
			<iframe name="preview" src="<?php echo $we_doc->Path; ?>" width="100%" height="95%" frameborder="no" border="0"></iframe>
			<?php
		} else{
			$parts = array();

			array_push($parts, array("headline" => g_l('weClass', "[preview]"), "html" => we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[no_preview_available]"), 1), "space" => 120));

			if($we_doc->ID){
				$_we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);
				$link = "<a href='" . getServerUrl() . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=" . (isset($_REQUEST['we_cmd'][0]) ? $_REQUEST['we_cmd'][0] : "") . "&we_cmd[1]=" . (isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : "") . "&we_cmd[2]=" . (isset($_REQUEST['we_cmd'][2]) ? $_REQUEST['we_cmd'][2] : "") . "&we_cmd[3]=download&we_transaction=" . $_we_transaction . "'>" . $http = $we_doc->getHttpPath() . "</a>";
			} else{
				$link = g_l('weClass', "[file_not_saved]");
			}
			array_push($parts, array("headline" => g_l('weClass', "[download]"), "html" => $link, "space" => 120));

			print we_multiIconBox::getHTML("weOtherDocPrev", "100%", $parts, 20);
		}
		?>

	</form>
</body>

</html>