<?php
/**
 * webEdition CMS
 *
 * $Rev: 5847 $
 * $Author: mokraemer $
 * $Date: 2013-02-19 20:54:58 +0100 (Tue, 19 Feb 2013) $
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
//	send charset, if one is set:
if(isset($we_doc->elements["Charset"]["dat"]) && $we_doc->elements["Charset"]["dat"] && $we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES){
	we_html_tools::headerCtCharset('text/html', $we_doc->elements["Charset"]["dat"]);
}
we_html_tools::protect();
we_html_tools::htmlTop();
echo we_html_element::jsScript(JS_DIR . 'windows.js');

include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET;
?>
</head>
<body bgcolor="white" marginwidth="15" marginheight="15" leftmargin="15" topmargin="15" onUnload="doUnload()">
	<form name="we_form" method="post" onsubmit="return false;"><?php
		echo we_class::hiddenTrans();


		$fields = $we_doc->getVariantFieldNames();

		$headline = array(
			array(
				'dat' => g_l('weClass', "[variant_fields]")
			)
		);


		$content = array();
		foreach($fields as $ind => $field){
			$element = $we_doc->getElement('variant_' . $field);
			$content[$ind] = array(array(
					'dat' => we_forms::checkboxWithHidden($element ? true : false, 'we_' . $we_doc->Name . "_variant[variant_$field]", $field, false, 'middlefont', '_EditorFrame.setEditorIsHot(true);')
			));
		}

		$parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[variant_info]'), 2, 620, false),
				'space' => 0,
				'noline' => 1
			),
			array(
				'headline' => '',
				'html' => we_html_tools::htmlDialogBorder3(600, 0, $content, $headline),
				'space' => 0,
				'noline' => 1
			)
		);

		print we_multiIconBox::getHTML("template_variant", "100%", $parts, 30, '', -1, '', '', false);
		?>
	</form>
</body>
</html>