<?php
/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
define("WE_EDIT_IMAGE", true);


we_html_tools::htmlTop();

if(isset($_REQUEST['we_cmd'][0]) && substr($_REQUEST['we_cmd'][0], 0, 15) == "doImage_convert"){
	print we_html_element::jsElement('parent.frames[0].we_setPath("' . $we_doc->Path . '","' . $we_doc->Text . '", "' . $we_doc->ID . '");');
}

echo we_html_element::jsScript(JS_DIR . 'windows.js');
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

print STYLESHEET;
?>
</head>

<body class="weEditorBody" style="padding:20px;">

	<form name="we_form" method="post" onsubmit="return false;">
		<?php
		echo we_class::hiddenTrans();
		$_headline = g_l('weClass', "[image]");

		$_gdtype = $we_doc->getGDType();

		$editselect = '<select name="editmenue" size="1" onchange="var cmnd = this.options[this.selectedIndex].value; if(cmnd){if(cmnd==\'doImage_convertPNG\' || cmnd==\'doImage_convertGIF\'){_EditorFrame.setEditorIsHot(true);};we_cmd(cmnd,\'' . $we_transaction . '\');}this.selectedIndex=0"' . (($we_doc->getElement("data") && we_image_edit::is_imagetype_read_supported($_gdtype) && we_image_edit::gd_version() > 0) ? "" : ' disabled="disabled"') . '>
<option value="">' . g_l('weClass', "[edit]") . '</option>
<option value="image_resize">' . g_l('weClass', "[resize]") . '&hellip;</option>
<option value="image_rotate">' . g_l('weClass', "[rotate]") . '&hellip;</option>
<option value="image_crop">' . g_l('weClass', "[crop]") . '&hellip;</option>
<option value="" disabled="disabled" style="color:grey">' . g_l('weClass', "[convert]") . '</option>
' . ((in_array("jpg", we_image_edit::supported_image_types())) ? '<option value="image_convertJPEG">&nbsp;&nbsp;' . g_l('weClass', "[convert_jpg]") . '...</option>' : '') . '
' . (($_gdtype != "gif" && in_array("gif", we_image_edit::supported_image_types())) ? '<option value="doImage_convertGIF">&nbsp;&nbsp;' . g_l('weClass', "[convert_gif]") . '</option>' : '') . '
' . (($_gdtype != "png" && in_array("png", we_image_edit::supported_image_types())) ? '<option value="doImage_convertPNG">&nbsp;&nbsp;' . g_l('weClass', "[convert_png]") . '</option>' : '') . '
</select>';
		$_html = '<table cellpadding="0" cellspacing="0" border="0">
';
		if($we_doc->EditPageNr == 15){
			$_html .= '<tr>
								<td>' . $editselect . '</td>
							</tr>
							<tr>
									<td>' . we_html_tools::getPixel(2, 10) . '</td>
							</tr>
							<tr>
									<td>' . we_html_tools::getPixel(2, 10) . '</td>
							</tr>
							';
		}

		$_html .= '
                        <tr>
							<td>' . $we_doc->getHtml(true) . '</td>
						</tr>

			';

		$_html .= '</table>';

		print $_html;

