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
we_html_tools::htmlTop();
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET;
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>

</head>
<body class="weEditorBody">
	<form name="we_form"><?php echo we_class::hiddenTrans(); ?>
		<table cellpadding="6" cellspacing="0" border="0">
			<?php
			$parts = array();

			if(we_image_edit::gd_version() > 0){

				$_doc = $we_doc->getDocument();
				$imgType = we_image_edit::detect_image_type('', $_doc);

				if(!$_doc){
					$parts[] = array("headline" => "",
						"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', '[no_image_uploaded]'), 2, 700),
						"space" => 0
					);
				} else if(we_image_edit::is_imagetype_read_supported($imgType)){

					// look if the fields origwidth & origheight exixts. If not get and set the values
					if((!isset($we_doc->elements['origwidth']['dat'])) || (!isset($we_doc->elements['origheight']['dat']))){
						$arr = $we_doc->getOrigSize();
						$we_doc->setElement('origwidth', $arr[0], 'attrib');
						$we_doc->setElement('origheight', $arr[1], 'attrib');
						unset($arr);
					}

					$thumbs = $we_doc->getThumbs();
					foreach($thumbs as $thumbid){

						$thumbObj = new we_thumbnail();
						$thumbObj->initByThumbID($thumbid, $we_doc->ID, $we_doc->Filename, $we_doc->Path, $we_doc->Extension, $we_doc->getElement('origwidth'), $we_doc->getElement('origheight'), $_doc);

						srand((double) microtime() * 1000000);
						$randval = rand();


						$useOrig = $thumbObj->isOriginal();

						if((!$useOrig) && $we_doc->ID && ($we_doc->DocChanged == false) && file_exists($thumbObj->getOutputPath(true))){
							$src = $thumbObj->getOutputPath(false) . '?rand=' . $randval;
						} else{
							$src = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&amp;we_cmd[1]=' .
								$we_doc->ContentType . '&amp;we_cmd[2]=' .
								$we_transaction . '&amp;we_cmd[3]=' . ($useOrig ? "" : $thumbid) . '&amp;rand=' . $randval;
						}


						$delbut = we_button::create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('del_thumb','" . $thumbid . "');", true, 30);

						$thumbnail = '<table border="0" cellpadding="0" cellspacing="0" width="570"><tr><td width="538"><img src="' . $src . '" width="' . $thumbObj->getOutputWidth() .
							'" height="' . $thumbObj->getOutputHeight() . '" border="0" /></td><td width="10">' . we_html_tools::getPixel(10, 2) . '</td><td width="22">' . $delbut . '</td></tr></table>';

						$parts[] = array("headline" => $thumbObj->getThumbName(),
							"html" => $thumbnail,
							"space" => 120
						);
					}
					$parts[] = array("headline" => "",
						"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', "[add_descriptiontext]"), 2, "700") . '<br><br>' . we_button::create_button("image:btn_add_thumbnail", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('add_thumbnail','" . $we_transaction . "');"),
						"space" => 0
					);
				} else{
					$parts[] = array("headline" => "",
						"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', "[format_not_supported]"), 2, "700"),
						"space" => 0
					);
				}
			} else{
				$parts[] = array("headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('thumbnails', "[add_description_nogdlib]"), 2, "700"),
					"space" => 0
				);
			}
			print we_multiIconBox::getJS() . we_multiIconBox::getHTML('', '100%', $parts, 20);
			?>

	</form>
</body>
</html>