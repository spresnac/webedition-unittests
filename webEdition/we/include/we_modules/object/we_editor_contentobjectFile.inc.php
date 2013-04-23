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
include_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

we_html_tools::protect();

if(isset($GLOBALS['we_doc']->Charset) && $GLOBALS['we_doc']->Charset){ //	send charset which might be determined in template
	$charset = $GLOBALS['we_doc']->Charset;
} else{
	$charset = DEFAULT_CHARSET;
}

we_html_tools::headerCtCharset('text/html', $charset);

$_editMode = (isset($_previewMode) && $_previewMode == 1 ? 0 : 1);
$parts = $GLOBALS['we_doc']->getFieldsHTML($_editMode);

if(is_array($GLOBALS['we_doc']->DefArray)){
	foreach($GLOBALS['we_doc']->DefArray as $n => $v){
		if(is_array($v)){
			if(isset($v["required"]) && $v["required"] && $_editMode){
				$parts[] = array(
					"headline" => "",
					"html" => '*' . g_l('global', "[required_fields]"),
					"space" => 0,
					"name" => str_replace('.', '', uniqid('', true)),
				);
				break;
			}
		}
	}
}

we_html_tools::htmlTop('', $charset, 5);
if($GLOBALS['we_doc']->CSS){
	$cssArr = makeArrayFromCSV($GLOBALS['we_doc']->CSS);
	foreach($cssArr as $cs){
		print we_html_element::cssLink(id_to_path($cs));
	}
}

$we_doc = $GLOBALS['we_doc'];

$jsGUI = new weOrderContainer("_EditorFrame.getContentEditor()", "objectEntry");
echo $jsGUI->getJS(WEBEDITION_DIR . "js");

echo we_multiIconBox::getJs();
?>

<script type="text/javascript">
	<!--
	function toggleObject(id) {
		var elem = document.getElementById(id);
		if(elem.style.display == "none") {
			elem.style.display = "block";
		} else {
			elem.style.display = "none";
		}
	}
	//-->
</script>
<?php
echo we_html_element::jsScript(JS_DIR . 'windows.js');
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET;
?>
</head>

<body class="weEditorBody" onUnload="doUnload()">
	<form name="we_form" method="post"><?php
echo we_class::hiddenTrans();

if($_editMode){

	echo we_multiIconBox::_getBoxStart("100%", g_l('weClass', "[edit]"), md5(uniqid(__FILE__, true)), 30) .
	$jsGUI->getContainer() .
	we_multiIconBox::_getBoxEnd("100%");

	foreach($parts as $idx => $part){

		echo '<div id="' . $part['name'] . '">
			<a name="f' . $part['name'] . '"></a>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td class="defaultfont" width="100%">
					<table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">
						<tr><td class="defaultfont">' . $part["html"] . '</td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;">' . we_html_tools::getPixel(1, 1) . '</div></td>
			</tr>
			</table>
			</div>' .
		we_html_element::jsElement('objectEntry.add(document, \'' . $part['name'] . '\', null);');
	}
} else{
	if($_SESSION['weS']['we_mode'] == "normal"){
		$_msg = "";
	}
	print we_SEEM::parseDocument(we_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false));
}
?>
	</form>
</body><?php echo we_html_element::jsElement('setTimeout("doScrollTo();",100);'); ?>

</html>